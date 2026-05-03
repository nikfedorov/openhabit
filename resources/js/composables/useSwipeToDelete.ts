import { onUnmounted, type Ref } from 'vue';

type SwipeToDeleteOptions = {
    contentEl: Ref<HTMLElement | null>;
    deleteBtnEl: Ref<HTMLElement | null>;
    onDeleteSwipe: () => void;
    onDeleteTap: () => void;
};

/**
 * Composable that enables swipe-to-delete touch gestures on a habit item.
 *
 * - Swipe left reveals a delete button (48px).
 * - Swipe past 75% of width auto-deletes (calls onDeleteSwipe).
 * - Tapping the revealed delete button calls onDeleteTap.
 */
export function useSwipeToDelete({
    contentEl,
    deleteBtnEl,
    onDeleteSwipe,
    onDeleteTap,
}: SwipeToDeleteOptions) {
    let startX = 0;
    let startY = 0;
    let currentX = 0;
    let swiping = false;
    let direction: 'horizontal' | 'vertical' | null = null;
    let revealed = false;

    function getIsRtl(): boolean {
        return getComputedStyle(document.documentElement).direction === 'rtl';
    }

    function onTouchStart(e: TouchEvent) {
        // Ignore touches that originate from the drag handle to avoid
        // conflicting with drag-and-drop sorting.
        const target = e.target;
        if (target instanceof Element && target.closest('.habit-drag-handle')) {
            swiping = false;
            return;
        }

        const touch = e.touches[0];
        startX = touch.clientX;
        startY = touch.clientY;
        swiping = true;
        direction = null;

        const isRtl = getIsRtl();
        if (revealed) {
            startX += isRtl ? -48 : 48;
        }

        const content = contentEl.value;
        if (content) {
            content.style.transition = 'none';
            content.style.willChange = 'transform';
        }
    }

    function onTouchMove(e: TouchEvent) {
        if (!swiping) return;

        const touch = e.touches[0];
        const dx = touch.clientX - startX;
        const dy = touch.clientY - startY;

        if (!resolveDirection(dx, dy)) return;

        if (direction === 'vertical') {
            cancelSwipe();
            return;
        }

        if (e.cancelable) e.preventDefault();

        const diff = clampToSwipeAxis(dx, getIsRtl());
        currentX = diff;
        applyTransform(diff);
    }

    /**
     * Decides the gesture axis once the touch crosses the dead zone.
     * Returns false while the gesture is still ambiguous.
     */
    function resolveDirection(dx: number, dy: number): boolean {
        if (direction) return true;
        if (Math.abs(dx) < 5 && Math.abs(dy) < 5) return false;
        direction = Math.abs(dx) > Math.abs(dy) ? 'horizontal' : 'vertical';
        return true;
    }

    function cancelSwipe() {
        swiping = false;
        const content = contentEl.value;
        if (content) {
            content.style.willChange = '';
        }
    }

    /**
     * Restricts the gesture delta to the deletion direction (left in LTR,
     * right in RTL).
     */
    function clampToSwipeAxis(dx: number, isRtl: boolean): number {
        if (isRtl) return dx < 0 ? 0 : dx;
        return dx > 0 ? 0 : dx;
    }

    function applyTransform(diff: number) {
        const content = contentEl.value;
        const deleteBtn = deleteBtnEl.value;
        if (content) {
            content.style.transform = `translateX(${diff.toString()}px)`;
        }
        if (deleteBtn) {
            deleteBtn.style.opacity = diff === 0 ? '0' : '1';
        }
    }

    function onTouchEnd() {
        if (!swiping) return;
        swiping = false;
        direction = null;

        const content = contentEl.value;
        if (!content) return;

        content.style.willChange = '';
        content.style.transition = 'transform 0.2s ease';

        const isRtl = getIsRtl();
        const w = content.parentElement?.offsetWidth ?? content.offsetWidth;

        if (Math.abs(currentX) > w * 0.75) {
            commitDelete(content, w, isRtl);
        } else if (Math.abs(currentX) > 48) {
            settleRevealed(content, isRtl);
        } else {
            settleClosed(content);
        }
    }

    function commitDelete(content: HTMLElement, width: number, isRtl: boolean) {
        revealed = false;
        content.style.transform = `translateX(${isRtl ? '' : '-'}${width.toString()}px)`;
        onDeleteSwipe();
    }

    function settleRevealed(content: HTMLElement, isRtl: boolean) {
        revealed = true;
        currentX = isRtl ? 48 : -48;
        content.style.transform = `translateX(${currentX.toString()}px)`;
    }

    function settleClosed(content: HTMLElement) {
        revealed = false;
        currentX = 0;
        content.style.transform = 'translateX(0)';
        const deleteBtn = deleteBtnEl.value;
        if (deleteBtn) {
            deleteBtn.style.opacity = '0';
        }
    }

    function onDeleteBtnClick() {
        onDeleteTap();
    }

    /**
     * Resets the swipe position back to closed state.
     */
    function resetSwipe() {
        const content = contentEl.value;
        const deleteBtn = deleteBtnEl.value;
        if (content) {
            content.style.transition = 'transform 0.2s ease';
            content.style.transform = 'translateX(0)';
        }
        if (deleteBtn) {
            deleteBtn.style.opacity = '0';
        }
        revealed = false;
        currentX = 0;
    }

    function setup() {
        const content = contentEl.value;
        const deleteBtn = deleteBtnEl.value;
        if (!content) return;

        content.addEventListener('touchstart', onTouchStart, { passive: true });
        content.addEventListener('touchmove', onTouchMove, { passive: false });
        content.addEventListener('touchend', onTouchEnd);

        if (deleteBtn) {
            deleteBtn.addEventListener('click', onDeleteBtnClick);
        }
    }

    function cleanup() {
        const content = contentEl.value;
        const deleteBtn = deleteBtnEl.value;
        if (content) {
            content.removeEventListener('touchstart', onTouchStart);
            content.removeEventListener('touchmove', onTouchMove);
            content.removeEventListener('touchend', onTouchEnd);
        }
        if (deleteBtn) {
            deleteBtn.removeEventListener('click', onDeleteBtnClick);
        }
    }

    onUnmounted(cleanup);

    return { setup, cleanup, resetSwipe };
}
