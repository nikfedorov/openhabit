import { mount } from '@vue/test-utils';
import { afterEach, describe, expect, it, vi } from 'vitest';
import { nextTick } from 'vue';
import PremiumModal from '@/components/PremiumModal.vue';
import { defaultTrial } from '@/tests/helpers/settings';

function mountPremiumModal(overrides: Partial<typeof defaultTrial> = {}) {
    return mount(PremiumModal, {
        attachTo: document.body,
        props: {
            show: true,
            trialData: {
                ...defaultTrial,
                shouldShowBanner: true,
                bannerText: 'Free trial: 3 days',
                invoiceLink: 'https://t.me/invoice',
                ...overrides,
            },
        },
    });
}

afterEach(() => {
    document.body.innerHTML = '';
    delete (window as unknown as Record<string, unknown>).Telegram;
});

describe('PremiumModal', () => {
    it('does not render when show is false', () => {
        mount(PremiumModal, {
            attachTo: document.body,
            props: { show: false, trialData: defaultTrial },
        });
        expect(
            document.body.querySelector('[data-testid="premium-modal"]'),
        ).toBeNull();
    });

    it('renders the premium copy when shown', () => {
        mountPremiumModal();

        expect(
            document.body.querySelector('[data-testid="premium-modal"]'),
        ).not.toBeNull();
        expect(document.body.textContent).toContain('Premium Features');
        expect(document.body.textContent).toContain(
            'Upgrade to unlock all features',
        );

        const list = document.body.querySelector(
            '[data-testid="premium-features-list"]',
        );

        expect(list?.textContent).toContain('Multiple reminders per habit');
        expect(list?.textContent).toContain('Daily AI digest of your progress');
        expect(list?.textContent).toContain('Export all your data as CSV');

        const btn = document.body.querySelector(
            '[data-testid="premium-modal-upgrade"]',
        );

        expect(btn?.textContent?.trim()).toBe('Upgrade');
    });

    it('emits close when the modal is dismissed', () => {
        const wrapper = mountPremiumModal();
        const closeBtn = document.body.querySelector(
            '[data-testid="premium-modal-close"]',
        ) as HTMLElement;

        closeBtn.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        const backdrop = document.body.querySelector(
            '.fixed.inset-0.bg-black\\/50',
        ) as HTMLElement;

        backdrop.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        const dialog = document.body.querySelector(
            '[data-testid="premium-modal"]',
        ) as HTMLElement;

        dialog.dispatchEvent(
            new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }),
        );

        expect(wrapper.emitted('close')).toHaveLength(3);
    });

    it('opens the invoice in Telegram WebApp when available', async () => {
        const openInvoice = vi.fn();

        Object.defineProperty(window, 'Telegram', {
            writable: true,
            configurable: true,
            value: { WebApp: { openInvoice } },
        });

        mountPremiumModal();

        const upgradeBtn = document.body.querySelector(
            '[data-testid="premium-modal-upgrade"]',
        ) as HTMLElement;
        upgradeBtn.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        await nextTick();

        expect(openInvoice).toHaveBeenCalledWith('https://t.me/invoice');
        expect(
            document.body.querySelector(
                '[data-testid="premium-modal-not-in-telegram"]',
            ),
        ).toBeNull();
    });

    it('opens the invoice in a new tab when Telegram WebApp is unavailable', async () => {
        const openSpy = vi.spyOn(window, 'open').mockImplementation(() => null);

        mountPremiumModal();

        const upgradeBtn = document.body.querySelector(
            '[data-testid="premium-modal-upgrade"]',
        ) as HTMLElement;
        upgradeBtn.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        await nextTick();

        expect(openSpy).toHaveBeenCalledWith('https://t.me/invoice', '_blank');
        expect(
            document.body.querySelector(
                '[data-testid="premium-modal-not-in-telegram"]',
            ),
        ).toBeNull();

        openSpy.mockRestore();
    });

    it('shows the Telegram hint when there is no invoice link', async () => {
        mountPremiumModal({ invoiceLink: null });

        const upgradeBtn = document.body.querySelector(
            '[data-testid="premium-modal-upgrade"]',
        ) as HTMLElement;
        upgradeBtn.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        await nextTick();

        expect(
            document.body.querySelector(
                '[data-testid="premium-modal-not-in-telegram"]',
            ),
        ).not.toBeNull();
    });
});
