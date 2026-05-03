import { describe, expect, it, vi } from 'vitest';
import { ref } from 'vue';
import { useSwipeToDelete } from '@/composables/useSwipeToDelete';

/**
 * Helper to mount the composable outside a real component lifecycle.
 * We suppress onUnmounted by stubbing it.
 */
vi.mock('vue', async () => {
    const actual = await vi.importActual<typeof import('vue')>('vue');
    return {
        ...actual,
        onUnmounted: vi.fn(),
    };
});

function createElements() {
    const content = document.createElement('div');
    const deleteBtn = document.createElement('button');
    const wrapper = document.createElement('div');
    wrapper.style.width = '400px';
    wrapper.appendChild(deleteBtn);
    wrapper.appendChild(content);
    Object.defineProperty(wrapper, 'offsetWidth', { value: 400 });
    Object.defineProperty(content, 'offsetWidth', { value: 400 });
    Object.defineProperty(content, 'parentElement', { value: wrapper });
    return { content, deleteBtn, wrapper };
}

function createTouch(clientX: number, clientY: number): Touch {
    return { clientX, clientY } as Touch;
}

function fireTouchStart(el: HTMLElement, x: number, y = 0) {
    el.dispatchEvent(
        new TouchEvent('touchstart', {
            touches: [createTouch(x, y)],
        }),
    );
}

function fireTouchMove(el: HTMLElement, x: number, y = 0) {
    const event = new TouchEvent('touchmove', {
        cancelable: true,
        touches: [createTouch(x, y)],
    });
    el.dispatchEvent(event);
    return event;
}

function fireTouchEnd(el: HTMLElement) {
    el.dispatchEvent(new TouchEvent('touchend'));
}

/**
 * Stubs `getComputedStyle` so the document's `direction` reads as RTL.
 * Restored automatically via `vi.restoreAllMocks()` in the test.
 */
function mockRtl() {
    const original = window.getComputedStyle;
    vi.spyOn(window, 'getComputedStyle').mockImplementation((el) => {
        const result = original(el);
        if (el === document.documentElement) {
            return { ...result, direction: 'rtl' } as CSSStyleDeclaration;
        }
        return result;
    });
}

type SwipeCallbacks = {
    onDeleteSwipe?: ReturnType<typeof vi.fn>;
    onDeleteTap?: ReturnType<typeof vi.fn>;
};

/**
 * Mounts the composable with fresh DOM elements, wires the listeners and
 * returns the produced refs and spies. Centralises the boilerplate shared
 * by every behavioural test in this suite.
 */
function setupSwipe(callbacks: SwipeCallbacks = {}) {
    const { content, deleteBtn, wrapper } = createElements();
    const contentEl = ref<HTMLElement | null>(content);
    const deleteBtnEl = ref<HTMLElement | null>(deleteBtn);
    const onDeleteSwipe = callbacks.onDeleteSwipe ?? vi.fn();
    const onDeleteTap = callbacks.onDeleteTap ?? vi.fn();

    const swipe = useSwipeToDelete({
        contentEl,
        deleteBtnEl,
        onDeleteSwipe,
        onDeleteTap,
    });
    swipe.setup();

    return {
        content,
        deleteBtn,
        wrapper,
        contentEl,
        deleteBtnEl,
        onDeleteSwipe,
        onDeleteTap,
        ...swipe,
    };
}

/**
 * Builds the composable without invoking `setup()`, optionally with a null
 * delete button. Useful for tests that need to install spies or refs before
 * the listeners are attached.
 */
function buildSwipe(opts: { withDeleteBtn?: boolean } = {}) {
    const { content, deleteBtn } = createElements();
    const contentEl = ref<HTMLElement | null>(content);
    const deleteBtnEl = ref<HTMLElement | null>(
        opts.withDeleteBtn === false ? null : deleteBtn,
    );

    const swipe = useSwipeToDelete({
        contentEl,
        deleteBtnEl,
        onDeleteSwipe: vi.fn(),
        onDeleteTap: vi.fn(),
    });

    return { content, deleteBtn, contentEl, deleteBtnEl, ...swipe };
}

describe('useSwipeToDelete', () => {
    it('sets up touch listeners on content element', () => {
        const { content, setup } = buildSwipe();
        const spy = vi.spyOn(content, 'addEventListener');
        setup();

        expect(spy).toHaveBeenCalledWith('touchstart', expect.any(Function), {
            passive: true,
        });
        expect(spy).toHaveBeenCalledWith('touchmove', expect.any(Function), {
            passive: false,
        });
        expect(spy).toHaveBeenCalledWith('touchend', expect.any(Function));
    });

    it('calls onDeleteTap when delete button is clicked', () => {
        const { content, deleteBtn, onDeleteTap } = setupSwipe();
        deleteBtn.click();

        expect(onDeleteTap).toHaveBeenCalledOnce();
    });

    it('resetSwipe resets transform to 0', () => {
        const { content, deleteBtn, resetSwipe } = setupSwipe();
        content.style.transform = 'translateX(-48px)';
        deleteBtn.style.opacity = '1';

        resetSwipe();

        expect(content.style.transform).toBe('translateX(0)');
        expect(deleteBtn.style.opacity).toBe('0');
    });

    it('cleans up listeners on cleanup call', () => {
        const { content, setup, cleanup } = buildSwipe();
        const removeSpy = vi.spyOn(content, 'removeEventListener');

        setup();
        cleanup();

        expect(removeSpy).toHaveBeenCalledWith(
            'touchstart',
            expect.any(Function),
        );
        expect(removeSpy).toHaveBeenCalledWith(
            'touchmove',
            expect.any(Function),
        );
        expect(removeSpy).toHaveBeenCalledWith(
            'touchend',
            expect.any(Function),
        );
    });

    it('translates content left on horizontal swipe', () => {
        const { content, deleteBtn } = setupSwipe();

        fireTouchStart(content, 200);
        fireTouchMove(content, 140); // -60px (negative = left)

        expect(content.style.transform).toBe('translateX(-60px)');
        expect(deleteBtn.style.opacity).toBe('1');
    });

    it('does not translate content right (positive direction)', () => {
        const { content, deleteBtn } = setupSwipe();

        fireTouchStart(content, 200);
        fireTouchMove(content, 260); // +60px (right = blocked in LTR)

        expect(content.style.transform).toBe('translateX(0px)');
        expect(deleteBtn.style.opacity).toBe('0');
    });

    it('reveals delete button on partial swipe (>48px)', () => {
        const { content, deleteBtn } = setupSwipe();

        fireTouchStart(content, 200);
        fireTouchMove(content, 140); // -60px > 48px threshold
        fireTouchEnd(content);

        expect(content.style.transform).toBe('translateX(-48px)');
    });

    it('snaps back on small swipe (<48px)', () => {
        const { content, deleteBtn } = setupSwipe();

        fireTouchStart(content, 200);
        fireTouchMove(content, 175); // -25px < 48px threshold
        fireTouchEnd(content);

        expect(content.style.transform).toBe('translateX(0)');
        expect(deleteBtn.style.opacity).toBe('0');
    });

    it('calls onDeleteSwipe on large swipe (>75% width)', () => {
        const { content, deleteBtn, onDeleteSwipe } = setupSwipe();

        fireTouchStart(content, 400);
        fireTouchMove(content, 80); // -320px > 75% of 400
        fireTouchEnd(content);

        expect(onDeleteSwipe).toHaveBeenCalledOnce();
    });

    it('ignores vertical swipes', () => {
        const { content, deleteBtn } = setupSwipe();

        fireTouchStart(content, 200, 200);
        fireTouchMove(content, 200, 140); // vertical movement only

        // Should not have set transform (vertical swipe is ignored)
        expect(content.style.transform).toBe('');
    });

    it('ignores tiny movements below 5px threshold', () => {
        const { content, deleteBtn } = setupSwipe();

        fireTouchStart(content, 200, 200);
        fireTouchMove(content, 202, 201); // dx=2, dy=1 — both < 5

        // No transform set since direction hasn't been determined
        expect(content.style.transform).toBe('');
    });

    it('does not call onDeleteSwipe if touchend without swiping', () => {
        const { content, deleteBtn, onDeleteSwipe } = setupSwipe();

        // Fire touchend without touchstart
        fireTouchEnd(content);

        expect(onDeleteSwipe).not.toHaveBeenCalled();
    });

    it('ignores touchmove without prior touchstart', () => {
        const { content, deleteBtn } = setupSwipe();

        // Fire touchmove without touchstart — swiping is false
        fireTouchMove(content, 140);

        expect(content.style.transform).toBe('');
    });

    it('handles null content element gracefully in setup', () => {
        const { deleteBtn } = createElements();
        const contentEl = ref<HTMLElement | null>(null);
        const deleteBtnEl = ref<HTMLElement | null>(deleteBtn);

        const { setup } = useSwipeToDelete({
            contentEl,
            deleteBtnEl,
            onDeleteSwipe: vi.fn(),
            onDeleteTap: vi.fn(),
        });

        // Should not throw
        setup();
    });

    it('handles null elements gracefully in cleanup/reset', () => {
        const contentEl = ref<HTMLElement | null>(null);
        const deleteBtnEl = ref<HTMLElement | null>(null);

        const { cleanup, resetSwipe } = useSwipeToDelete({
            contentEl,
            deleteBtnEl,
            onDeleteSwipe: vi.fn(),
            onDeleteTap: vi.fn(),
        });

        // Should not throw
        cleanup();
        resetSwipe();
    });

    it('handles non-cancelable touchmove events', () => {
        const { content, deleteBtn } = setupSwipe();

        fireTouchStart(content, 200);

        // Fire a non-cancelable touchmove
        const event = new TouchEvent('touchmove', {
            cancelable: false,
            touches: [createTouch(140, 0)],
        });
        content.dispatchEvent(event);

        expect(content.style.transform).toBe('translateX(-60px)');
    });

    it('handles RTL direction (blocks negative, allows positive)', () => {
        mockRtl();
        const { content } = setupSwipe();

        // In RTL, swiping right (positive diff) should work
        fireTouchStart(content, 200);
        fireTouchMove(content, 260); // +60px (allowed in RTL)

        expect(content.style.transform).toBe('translateX(60px)');

        fireTouchEnd(content);

        // Should reveal at +48px
        expect(content.style.transform).toBe('translateX(48px)');

        vi.restoreAllMocks();
    });

    it('blocks left swipe in RTL mode', () => {
        mockRtl();
        const { content, deleteBtn } = setupSwipe();

        fireTouchStart(content, 200);
        fireTouchMove(content, 140); // -60px (blocked in RTL)

        expect(content.style.transform).toBe('translateX(0px)');
        expect(deleteBtn.style.opacity).toBe('0');

        vi.restoreAllMocks();
    });

    it('auto-deletes on large swipe in RTL mode', () => {
        mockRtl();
        const { content, onDeleteSwipe } = setupSwipe();

        fireTouchStart(content, 50);
        fireTouchMove(content, 370); // +320px > 75% of 400
        fireTouchEnd(content);

        expect(onDeleteSwipe).toHaveBeenCalledOnce();

        vi.restoreAllMocks();
    });

    it('adjusts start position when already revealed in RTL', () => {
        mockRtl();
        const { content } = setupSwipe();

        // First swipe to reveal
        fireTouchStart(content, 200);
        fireTouchMove(content, 260);
        fireTouchEnd(content);

        // Second swipe from revealed position
        fireTouchStart(content, 200);
        fireTouchMove(content, 260);
        fireTouchEnd(content);

        expect(content.style.transform).toBe('translateX(48px)');

        vi.restoreAllMocks();
    });

    it('adjusts start position when already revealed', () => {
        const { content, deleteBtn } = setupSwipe();

        // First swipe to reveal
        fireTouchStart(content, 200);
        fireTouchMove(content, 140); // -60px
        fireTouchEnd(content);
        // Now revealed at -48px

        // Second swipe - should account for the revealed offset
        fireTouchStart(content, 200);
        fireTouchMove(content, 140);
        fireTouchEnd(content);

        // Should still be at -48px (not double-revealed)
        expect(content.style.transform).toBe('translateX(-48px)');
    });

    it('sets up without delete button element', () => {
        const { content, setup } = buildSwipe({ withDeleteBtn: false });
        const spy = vi.spyOn(content, 'addEventListener');
        setup();

        // Content listeners are set up
        expect(spy).toHaveBeenCalledWith('touchstart', expect.any(Function), {
            passive: true,
        });
        // No delete button click listener since element is null
    });

    it('snaps back without delete button element', () => {
        const { content, setup } = buildSwipe({ withDeleteBtn: false });
        setup();

        fireTouchStart(content, 200);
        fireTouchMove(content, 175); // -25px < 48px threshold
        fireTouchEnd(content);

        expect(content.style.transform).toBe('translateX(0)');
    });

    it('handles content ref becoming null before touchend', () => {
        const { content, contentEl } = setupSwipe();

        fireTouchStart(content, 200);
        fireTouchMove(content, 140);

        // Set content ref to null before touchend
        contentEl.value = null;
        fireTouchEnd(content);

        // Should not throw
    });

    it('handles content ref becoming null during touchmove', () => {
        const { content, contentEl } = setupSwipe();

        fireTouchStart(content, 200);

        // Set content ref to null before touchmove
        contentEl.value = null;
        fireTouchMove(content, 140);

        // Should not throw, no transform set on null element
    });

    it('snaps back in RTL with small swipe', () => {
        const { content, deleteBtn } = createElements();
        const contentEl = ref<HTMLElement | null>(content);
        const deleteBtnEl = ref<HTMLElement | null>(deleteBtn);
        mockRtl();

        const { setup } = useSwipeToDelete({
            contentEl,
            deleteBtnEl,
            onDeleteSwipe: vi.fn(),
            onDeleteTap: vi.fn(),
        });

        setup();

        fireTouchStart(content, 200);
        fireTouchMove(content, 225); // +25px < 48px threshold in RTL
        fireTouchEnd(content);

        expect(content.style.transform).toBe('translateX(0)');
        expect(deleteBtn.style.opacity).toBe('0');

        vi.restoreAllMocks();
    });

    it('cleans up without delete button element', () => {
        const { content, setup, cleanup } = buildSwipe({
            withDeleteBtn: false,
        });
        const removeSpy = vi.spyOn(content, 'removeEventListener');

        setup();
        cleanup();

        expect(removeSpy).toHaveBeenCalledWith(
            'touchend',
            expect.any(Function),
        );
    });

    it('handles content ref null during touchstart', () => {
        const { content, contentEl } = setupSwipe();

        // Null out content ref before dispatching touchstart
        contentEl.value = null;
        fireTouchStart(content, 200);

        // Should not throw
    });

    it('continues swiping when direction already determined', () => {
        const { content, deleteBtn } = setupSwipe();

        fireTouchStart(content, 200);
        fireTouchMove(content, 190); // First move: determines direction as horizontal
        fireTouchMove(content, 140); // Second move: direction already set, skips detection

        expect(content.style.transform).toBe('translateX(-60px)');
    });

    it('handles content ref null during vertical swipe detection', () => {
        const { content, contentEl } = setupSwipe();

        fireTouchStart(content, 200, 200);

        // Null out content before vertical move is detected
        contentEl.value = null;
        fireTouchMove(content, 200, 140); // vertical movement

        // Should not throw, willChange not set since content is null
    });

    it('falls back to content offsetWidth when parentElement is missing', () => {
        const content = document.createElement('div');
        const deleteBtn = document.createElement('button');
        Object.defineProperty(content, 'offsetWidth', { value: 300 });
        Object.defineProperty(content, 'parentElement', { value: null });
        const contentEl = ref<HTMLElement | null>(content);
        const deleteBtnEl = ref<HTMLElement | null>(deleteBtn);
        const onDeleteSwipe = vi.fn();

        const { setup } = useSwipeToDelete({
            contentEl,
            deleteBtnEl,
            onDeleteSwipe,
            onDeleteTap: vi.fn(),
        });

        setup();

        // Swipe more than 75% of 300 = 225
        fireTouchStart(content, 300);
        fireTouchMove(content, 50); // -250px > 225
        fireTouchEnd(content);

        expect(onDeleteSwipe).toHaveBeenCalledOnce();
    });
});
