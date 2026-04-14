import { mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { defineComponent, onMounted } from 'vue';
import {
    isTelegram,
    useTelegramBackButton,
} from '@/composables/useTelegramBackButton';

// Helper component that calls the composable
function makeComponent(onBack: () => void) {
    return defineComponent({
        setup() {
            useTelegramBackButton(onBack);
        },
        template: '<div></div>',
    });
}

describe('isTelegram', () => {
    afterEach(() => {
        delete (window as Window & { Telegram?: unknown }).Telegram;
    });

    it('returns false when Telegram is not present', () => {
        expect(isTelegram()).toBe(false);
    });

    it('returns false when initData is empty', () => {
        (window as Window & { Telegram?: unknown }).Telegram = {
            WebApp: { initData: '' },
        };
        expect(isTelegram()).toBe(false);
    });

    it('returns true when initData is non-empty', () => {
        (window as Window & { Telegram?: unknown }).Telegram = {
            WebApp: { initData: 'query_id=AAE' },
        };
        expect(isTelegram()).toBe(true);
    });
});

describe('useTelegramBackButton', () => {
    const mockShow = vi.fn();
    const mockHide = vi.fn();
    const mockOnClick = vi.fn();
    const mockOffClick = vi.fn();

    function setTelegram(available: boolean) {
        if (!available) {
            delete (window as Window & { Telegram?: unknown }).Telegram;
            return;
        }
        (window as Window & { Telegram?: unknown }).Telegram = {
            WebApp: {
                initData: 'query_id=AAE',
                isVersionAtLeast: () => true,
                BackButton: {
                    isVisible: false,
                    show: mockShow,
                    hide: mockHide,
                    onClick: mockOnClick,
                    offClick: mockOffClick,
                },
            },
        };
    }

    beforeEach(() => {
        vi.clearAllMocks();
    });

    afterEach(() => {
        delete (window as Window & { Telegram?: unknown }).Telegram;
    });

    it('does nothing when not in Telegram', () => {
        setTelegram(false);
        const onBack = vi.fn();
        const wrapper = mount(makeComponent(onBack));

        expect(mockShow).not.toHaveBeenCalled();
        expect(mockOnClick).not.toHaveBeenCalled();

        wrapper.unmount();
        expect(mockHide).not.toHaveBeenCalled();
        expect(mockOffClick).not.toHaveBeenCalled();
    });

    it('shows BackButton and registers handler on mount', () => {
        setTelegram(true);
        const onBack = vi.fn();
        mount(makeComponent(onBack));

        expect(mockShow).toHaveBeenCalledOnce();
        expect(mockOnClick).toHaveBeenCalledWith(onBack);
    });

    it('hides BackButton and unregisters handler on unmount', () => {
        setTelegram(true);
        const onBack = vi.fn();
        const wrapper = mount(makeComponent(onBack));

        wrapper.unmount();

        expect(mockOffClick).toHaveBeenCalledWith(onBack);
        expect(mockHide).toHaveBeenCalledOnce();
    });
});
