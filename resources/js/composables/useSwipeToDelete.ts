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

        if (!direction) {
            if (Math.abs(dx) < 5 && Math.abs(dy) < 5) return;
            direction = Math.abs(dx) > Math.abs(dy) ? 'horizontal' : 'vertical';
        }

        if (direction === 'vertical') {
            swiping = false;
            const content = contentEl.value;
            if (content) {
                content.style.willChange = '';
            }
            return;
        }

        if (e.cancelable) e.preventDefault();

        const isRtl = getIsRtl();
        let diff = dx;
        if (isRtl) {
            if (diff < 0) diff = 0;
        } else {
            if (diff > 0) diff = 0;
        }

        currentX = diff;

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
            revealed = false;
            content.style.transform = `translateX(${isRtl ? '' : '-'}${w.toString()}px)`;
            onDeleteSwipe();
        } else if (Math.abs(currentX) > 48) {
            revealed = true;
            currentX = isRtl ? 48 : -48;
            content.style.transform = `translateX(${(isRtl ? 48 : -48).toString()}px)`;
        } else {
            revealed = false;
            currentX = 0;
            content.style.transform = 'translateX(0)';
            const deleteBtn = deleteBtnEl.value;
            if (deleteBtn) {
                deleteBtn.style.opacity = '0';
            }
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
