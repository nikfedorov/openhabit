import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import Edit from '@/pages/Edit.vue';
import {
    makeEditHabit,
    makeEditTranslations,
    makeTemplateHabit,
} from '@/tests/helpers/edit';
import { defaultTrial } from '@/tests/helpers/settings';

const { mockApiFetch, mockRouterPush } = vi.hoisted(() => ({
    mockApiFetch: vi.fn(),
    mockRouterPush: vi.fn(),
}));

vi.mock('@/utils/api', () => ({
    apiFetch: mockApiFetch,
}));

vi.mock('vue-router', () => ({
    useRouter: () => ({ push: mockRouterPush }),
}));

const defaultApiResponse = {
    data: [makeEditHabit({ id: 1, name: 'Run' })],
    translations: makeEditTranslations(),
    navigationTranslations: { track: 'Track', view: 'View', edit: 'Edit' },
    settings: {
        locale: 'en',
        theme: 'system',
        moveCompletedToEnd: true,
        trial: defaultTrial,
    },
    templates: [] as ReturnType<typeof makeTemplateHabit>[],
};

async function mountEdit(apiResponse = defaultApiResponse) {
    mockApiFetch.mockResolvedValueOnce(apiResponse);
    const wrapper = mount(Edit, {
        global: {
            stubs: { Teleport: true },
        },
    });
    await flushPromises();
    return wrapper;
}

beforeEach(() => {
    mockApiFetch.mockReset();
    mockRouterPush.mockReset();
});

describe('Edit Page', () => {
    it('fetches habits on mount and renders them', async () => {
        const wrapper = await mountEdit();
        expect(mockApiFetch).toHaveBeenCalledWith('/api/edit');
        expect(wrapper.text()).toContain('Run');
        expect(wrapper.text()).toContain('Habits');
    });

    it('emits ready and navigation-translations after load', async () => {
        const wrapper = await mountEdit();
        expect(wrapper.emitted('ready')).toBeTruthy();
        expect(wrapper.emitted('navigation-translations')).toBeTruthy();
        expect(wrapper.emitted('settings')).toBeTruthy();
    });

    it('shows empty state when no habits', async () => {
        const wrapper = await mountEdit({
            ...defaultApiResponse,
            data: [],
        });
        expect(wrapper.text()).toContain('No habits yet');
        expect(wrapper.text()).toContain('Create your first habit');
    });

    it('navigates to create page on new habit click', async () => {
        const wrapper = await mountEdit();
        const newBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('New Habit'))!;
        await newBtn.trigger('click');
        expect(mockRouterPush).toHaveBeenCalledWith({ name: 'edit.create' });
    });

    it('navigates to edit page on habit edit', async () => {
        const wrapper = await mountEdit();
        // Click the content button (second button in EditHabitItem)
        const editItem = wrapper.findComponent({ name: 'EditHabitItem' });
        editItem.vm.$emit('edit', 1);
        await flushPromises();
        expect(mockRouterPush).toHaveBeenCalledWith({
            name: 'edit.habit',
            params: { id: '1' },
        });
    });

    it('toggles habit active state optimistically', async () => {
        const wrapper = await mountEdit();
        mockApiFetch.mockResolvedValueOnce(undefined);
        const editItem = wrapper.findComponent({ name: 'EditHabitItem' });
        editItem.vm.$emit('toggle-active', 1);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/edit/habits/1/toggle',
            { method: 'POST' },
            { silent: true },
        );
    });

    it('deletes habit optimistically', async () => {
        const wrapper = await mountEdit();
        mockApiFetch.mockResolvedValueOnce(undefined);
        const editItem = wrapper.findComponent({ name: 'EditHabitItem' });
        editItem.vm.$emit('delete', 1);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/edit/habits/1',
            { method: 'DELETE' },
            { silent: true },
        );
    });

    it('still calls delete API when habit id is not found (no-op splice)', async () => {
        const wrapper = await mountEdit();
        mockApiFetch.mockResolvedValueOnce(undefined);
        const editItem = wrapper.findComponent({ name: 'EditHabitItem' });
        editItem.vm.$emit('delete', 999);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/edit/habits/999',
            { method: 'DELETE' },
            { silent: true },
        );
    });

    it('renders franklin section when franklin habits exist', async () => {
        const wrapper = await mountEdit({
            ...defaultApiResponse,
            data: [
                ...defaultApiResponse.data,
                makeEditHabit({
                    id: 2,
                    name: 'Temperance',
                    is_franklin_virtue: true,
                }),
            ],
        });
        expect(wrapper.text()).toContain("Franklin's Virtues");
    });

    it('toggles active on habit not found (no-op)', async () => {
        const wrapper = await mountEdit();
        mockApiFetch.mockResolvedValueOnce(undefined);
        const editItem = wrapper.findComponent({ name: 'EditHabitItem' });
        editItem.vm.$emit('toggle-active', 999);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/edit/habits/999/toggle',
            { method: 'POST' },
            { silent: true },
        );
    });

    it('toggles all franklin habits active state', async () => {
        const wrapper = await mountEdit({
            ...defaultApiResponse,
            data: [
                ...defaultApiResponse.data,
                makeEditHabit({
                    id: 10,
                    name: 'Temperance',
                    is_franklin_virtue: true,
                    is_active: true,
                }),
                makeEditHabit({
                    id: 11,
                    name: 'Silence',
                    is_franklin_virtue: true,
                    is_active: false,
                }),
            ],
        });
        mockApiFetch.mockResolvedValueOnce(undefined);
        const franklinSection = wrapper.findComponent({
            name: 'FranklinSection',
        });
        franklinSection.vm.$emit('toggle-all-active');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/edit/toggle-franklin',
            { method: 'POST' },
            { silent: true },
        );
    });

    it('renders template section when templates exist', async () => {
        const templates = [
            makeTemplateHabit({ id: 1, name: 'Exercise', category: 'Health' }),
        ];
        const wrapper = await mountEdit({
            ...defaultApiResponse,
            templates,
        });
        expect(
            wrapper.findComponent({ name: 'TemplateSection' }).exists(),
        ).toBe(true);
    });

    it('hides template section when no templates', async () => {
        const wrapper = await mountEdit();
        expect(
            wrapper.findComponent({ name: 'TemplateSection' }).exists(),
        ).toBe(false);
    });

    it('copies template and adds new habit to list', async () => {
        const templates = [
            makeTemplateHabit({
                id: 10,
                name: 'Exercise',
                human_readable: 'Every day',
                iterations_required: 1,
                sort_order: 5,
            }),
        ];
        const wrapper = await mountEdit({
            ...defaultApiResponse,
            templates,
        });

        const serverHabits = [
            ...defaultApiResponse.data,
            makeEditHabit({ id: 50, name: 'Exercise' }),
        ];
        mockApiFetch.mockResolvedValueOnce({ data: serverHabits });

        const templateSection = wrapper.findComponent({
            name: 'TemplateSection',
        });
        templateSection.vm.$emit('copy', 10);
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/edit/templates/10/copy',
            { method: 'POST' },
        );
        // Server response replaces the optimistic habit
        expect(wrapper.text()).toContain('Exercise');
    });

    it('ignores copy when template is not found', async () => {
        const templates = [makeTemplateHabit({ id: 10 })];
        const wrapper = await mountEdit({
            ...defaultApiResponse,
            templates,
        });

        const templateSection = wrapper.findComponent({
            name: 'TemplateSection',
        });
        templateSection.vm.$emit('copy', 999);
        await flushPromises();

        // Only the initial loadData call, no copy request
        expect(mockApiFetch).toHaveBeenCalledTimes(1);
    });

    it('still calls copy API even when backend returns no new habit', async () => {
        const templates = [makeTemplateHabit({ id: 10 })];
        const wrapper = await mountEdit({
            ...defaultApiResponse,
            templates,
        });

        // Backend returns the same habits, no newly added item.
        mockApiFetch.mockResolvedValueOnce({ data: defaultApiResponse.data });
        wrapper.findComponent({ name: 'TemplateSection' }).vm.$emit('copy', 10);
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/edit/templates/10/copy',
            { method: 'POST' },
        );
    });

    it('shows confirm dialog when confirm-delete is emitted', async () => {
        const wrapper = await mountEdit();
        const editItem = wrapper.findComponent({ name: 'EditHabitItem' });
        editItem.vm.$emit('confirm-delete', 1);
        await flushPromises();
        expect(
            wrapper.find('[data-testid="delete-confirm-modal"]').exists(),
        ).toBe(true);
        expect(wrapper.text()).toContain('Delete Habit');
    });

    it('deletes habit when confirm button is clicked', async () => {
        const wrapper = await mountEdit();
        mockApiFetch.mockResolvedValueOnce(undefined);
        const editItem = wrapper.findComponent({ name: 'EditHabitItem' });
        editItem.vm.$emit('confirm-delete', 1);
        await flushPromises();

        const confirmBtn = wrapper.find('[data-testid="delete-confirm-btn"]');
        await confirmBtn.trigger('click');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/edit/habits/1',
            { method: 'DELETE' },
            { silent: true },
        );
        expect(
            wrapper.find('[data-testid="delete-confirm-modal"]').exists(),
        ).toBe(false);
    });

    it('closes confirm dialog on cancel without deleting', async () => {
        const wrapper = await mountEdit();
        const editItem = wrapper.findComponent({ name: 'EditHabitItem' });
        editItem.vm.$emit('confirm-delete', 1);
        await flushPromises();

        const cancelBtn = wrapper.find('[data-testid="delete-cancel-btn"]');
        await cancelBtn.trigger('click');
        await flushPromises();

        expect(
            wrapper.find('[data-testid="delete-confirm-modal"]').exists(),
        ).toBe(false);
        // Should not have made any additional API calls beyond initial load
        expect(mockApiFetch).toHaveBeenCalledTimes(1);
    });

    it('still deletes directly when delete event is emitted (swipe)', async () => {
        const wrapper = await mountEdit();
        mockApiFetch.mockResolvedValueOnce(undefined);
        const editItem = wrapper.findComponent({ name: 'EditHabitItem' });
        editItem.vm.$emit('delete', 1);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/edit/habits/1',
            { method: 'DELETE' },
            { silent: true },
        );
    });

    it('highlights newly added habit after copy', async () => {
        vi.useFakeTimers();
        const templates = [makeTemplateHabit({ id: 10, name: 'Exercise' })];
        const wrapper = await mountEdit({
            ...defaultApiResponse,
            templates,
        });

        const newlyAddedHabit = makeEditHabit({ id: 50, name: 'Exercise' });
        mockApiFetch.mockResolvedValueOnce({
            data: [...defaultApiResponse.data, newlyAddedHabit],
        });

        wrapper.findComponent({ name: 'TemplateSection' }).vm.$emit('copy', 10);
        await flushPromises();

        const items = wrapper.findAllComponents({ name: 'EditHabitItem' });
        const newItem = items.find((i) => i.props('habit').id === 50);
        expect(newItem?.props('highlighted')).toBe(true);

        vi.advanceTimersByTime(5000);
        await flushPromises();

        const itemsAfter = wrapper.findAllComponents({ name: 'EditHabitItem' });
        const newItemAfter = itemsAfter.find((i) => i.props('habit').id === 50);
        expect(newItemAfter?.props('highlighted')).toBe(false);
        vi.useRealTimers();
    });

    it('keeps all habits highlighted simultaneously when multiple copies happen', async () => {
        vi.useFakeTimers();
        const templates = [
            makeTemplateHabit({ id: 10, name: 'A' }),
            makeTemplateHabit({ id: 11, name: 'B' }),
        ];
        const wrapper = await mountEdit({
            ...defaultApiResponse,
            templates,
        });

        // First copy adds habit 50.
        mockApiFetch.mockResolvedValueOnce({
            data: [...defaultApiResponse.data, makeEditHabit({ id: 50 })],
        });
        wrapper.findComponent({ name: 'TemplateSection' }).vm.$emit('copy', 10);
        await flushPromises();

        // Second copy 2s later adds habit 51.
        vi.advanceTimersByTime(2000);
        mockApiFetch.mockResolvedValueOnce({
            data: [
                ...defaultApiResponse.data,
                makeEditHabit({ id: 50 }),
                makeEditHabit({ id: 51 }),
            ],
        });
        wrapper.findComponent({ name: 'TemplateSection' }).vm.$emit('copy', 11);
        await flushPromises();

        // Both habits must be highlighted at the same time.
        const allItems = wrapper.findAllComponents({ name: 'EditHabitItem' });
        expect(
            allItems
                .find((i) => i.props('habit').id === 50)
                ?.props('highlighted'),
        ).toBe(true);
        expect(
            allItems
                .find((i) => i.props('habit').id === 51)
                ?.props('highlighted'),
        ).toBe(true);

        // 3s more → first timer fires (5s total), habit 50 clears; habit 51 still lit.
        vi.advanceTimersByTime(3000);
        await flushPromises();

        const items2 = wrapper.findAllComponents({ name: 'EditHabitItem' });
        expect(
            items2
                .find((i) => i.props('habit').id === 50)
                ?.props('highlighted'),
        ).toBe(false);
        expect(
            items2
                .find((i) => i.props('habit').id === 51)
                ?.props('highlighted'),
        ).toBe(true);

        vi.useRealTimers();
    });

    it('sends reorder request with current order when drag ends', async () => {
        const wrapper = await mountEdit({
            ...defaultApiResponse,
            data: [
                makeEditHabit({ id: 1, name: 'A' }),
                makeEditHabit({ id: 2, name: 'B' }),
                makeEditHabit({ id: 3, name: 'C' }),
            ],
        });
        mockApiFetch.mockResolvedValueOnce(undefined);

        await (
            wrapper.vm as unknown as { reorderHabits: () => Promise<void> }
        ).reorderHabits();

        expect(mockApiFetch).toHaveBeenLastCalledWith(
            '/api/edit/habits/reorder',
            {
                method: 'POST',
                body: JSON.stringify({ ordered_ids: [1, 2, 3] }),
            },
            { silent: true },
        );
    });
});
