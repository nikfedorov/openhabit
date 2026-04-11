import { ref, type Ref } from 'vue';
import type { ApiResponse, UserSettings } from '@/types/api';
import type { Habit, TrackData } from '@/types/track';
import { apiFetch } from '@/utils/api';

/**
 * Sorts habits so uncompleted ones come first, both groups ordered by sort_order.
 * When moveCompletedToEnd is false, the original order is preserved.
 */
function sortHabits(habits: Habit[], moveCompletedToEnd: boolean): Habit[] {
    if (!moveCompletedToEnd) {
        return habits;
    }

    const byOrder = (a: Habit, b: Habit) => a.sort_order - b.sort_order;
    const uncompleted = habits.filter((h) => !h.is_completed).sort(byOrder);
    const completed = habits.filter((h) => h.is_completed).sort(byOrder);

    return [...uncompleted, ...completed];
}

/**
 * Mutates habit and trackData in-place to reflect a toggled state.
 *
 * Toggle semantics:
 * - Completing: increment current_iteration up to iterations_required.
 *   Mark is_completed when the cap is reached.
 * - Un-completing: decrement current_iteration (floor 0), clear is_completed.
 *
 * After mutating the habit, re-sorts the full habits list so the UI
 * immediately reflects the new completed/uncompleted grouping.
 */
function applyOptimisticToggle(habit: Habit, trackData: TrackData, moveCompletedToEnd: boolean): void {
    if (habit.is_completed) {
        // Un-complete: step back one iteration
        habit.is_completed = false;
        habit.current_iteration = Math.max(0, habit.current_iteration - 1);
        trackData.completedCount = Math.max(0, trackData.completedCount - 1);
    } else {
        // Complete: advance one iteration; mark done when cap is reached
        habit.current_iteration = Math.min(
            habit.iterations_required,
            habit.current_iteration + 1,
        );

        habit.is_completed =
            habit.current_iteration >= habit.iterations_required;
        if (habit.is_completed) {
            trackData.completedCount++;
        }
    }

    trackData.habits = sortHabits(
        trackData.habits,
        moveCompletedToEnd,
    );
}

/**
 * Manages optimistic habit toggling with in-flight tracking and rollback.
 *
 * Flow for a single toggle:
 *   1. Deep-clone current data as a rollback snapshot.
 *   2. Apply the toggle to the live ref immediately (optimistic UI).
 *   3. Send the API request.
 *   4. On success — if this was the last in-flight request for this habit,
 *      replace the ref with the authoritative server response.
 *   5. On error — if all requests for this habit have settled, restore
 *      the snapshot taken before the very first toggle.
 *
 * Handling rapid repeat toggles on the same habit:
 *   - Each tap increments an in-flight counter so intermediate responses
 *     are discarded (stale data is never written back to the ref).
 *   - The rollback snapshot is taken only on the *first* tap; subsequent
 *     taps reuse it so a full error always restores the original state.
 *   - The snapshot is only cleared once the habit's counter reaches zero,
 *     ensuring it survives for the duration of all concurrent requests.
 */
export function useHabitToggle(data: Ref<TrackData | null>, settings: Ref<UserSettings>) {
    // Reactive set — drives the shimmer/pending indicator in HabitItem
    const pendingHabitIds = ref(new Set<number>());

    // Non-reactive: counts how many API requests are currently in-flight per habit
    const inflightCount = new Map<number, number>();

    // Non-reactive: pre-toggle snapshot keyed by habitId, used for error rollback
    const rollbackSnapshots = new Map<number, TrackData>();

    /**
     * Registers a new in-flight request for a habit.
     * Saves a rollback snapshot only on the first tap (before any optimistic mutation).
     */
    function startInflight(habitId: number, snapshot: TrackData): void {
        if (!inflightCount.has(habitId)) {
            rollbackSnapshots.set(habitId, snapshot);
        }
        inflightCount.set(habitId, (inflightCount.get(habitId) ?? 0) + 1);
        pendingHabitIds.value.add(habitId);
    }

    /**
     * Decrements the in-flight counter for a habit.
     * Clears the pending indicator when the counter reaches zero.
     * Returns true when *all* habits (not just this one) have settled —
     * the caller uses this to decide whether to write server data back.
     */
    function resolveInflight(habitId: number): boolean {
        const count = inflightCount.get(habitId)!;
        if (count <= 1) {
            inflightCount.delete(habitId);
            pendingHabitIds.value.delete(habitId);
        } else {
            inflightCount.set(habitId, count - 1);
        }

        return inflightCount.size === 0;
    }

    /**
     * Toggles a habit: applies an optimistic update immediately, sends the
     * API request, and reconciles with the server response when it arrives.
     */
    async function toggle(habitId: number) {
        const currentData = data.value;
        const habit = currentData?.habits.find((h) => h.id === habitId);
        if (!currentData || !habit) {
            return;
        }

        // Snapshot before any mutation so we can roll back on error
        const snapshot = JSON.parse(JSON.stringify(currentData)) as TrackData;
        applyOptimisticToggle(habit, currentData, settings.value.moveCompletedToEnd);
        startInflight(habitId, snapshot);

        try {
            const response = await apiFetch<ApiResponse<TrackData>>(
                '/api/track/toggle',
                {
                    method: 'POST',
                    body: JSON.stringify({
                        habit_id: habitId,
                        date: snapshot.date,
                    }),
                },
                { silent: true },
            );

            const allSettled = resolveInflight(habitId);

            // Drop the snapshot now that this habit's requests are done
            if (!inflightCount.has(habitId)) {
                rollbackSnapshots.delete(habitId);
            }

            // Only write the server response once every in-flight request
            // has settled — prevents a stale earlier response from
            // overwriting a more recent optimistic state.
            if (allSettled) {
                data.value = response.data;
            }
        } catch {
            const allSettled = resolveInflight(habitId);

            // Restore the original snapshot only after all requests settle,
            // so a partial series of errors doesn't leave the UI in a
            // half-rolled-back state.
            if (allSettled) {
                data.value = rollbackSnapshots.get(habitId) as TrackData;
                rollbackSnapshots.delete(habitId);
            }
        }
    }

    return { pendingHabitIds, toggle };
}
