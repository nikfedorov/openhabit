import { mount } from '@vue/test-utils';
import { afterEach, describe, expect, it, vi } from 'vitest';
import PremiumModal from '@/components/PremiumModal.vue';
import type { TrialData } from '@/types/api';

const defaultTrialData: TrialData = {
    shouldShowBanner: true,
    bannerText: 'Free trial: 3 days',
    invoiceLink: 'https://t.me/invoice',
    learnMore: 'Learn more',
    featuresTitle: 'Premium Features',
    featuresSubtitle: 'Upgrade to unlock all features',
    featureNotifications: 'Multiple reminders per habit',
    featureAiDigest: 'Daily AI digest of your progress',
    featureExport: 'Export all your data as CSV',
    upgradeLabel: 'Upgrade',
    openInTelegramLabel: 'To upgrade, please open the app in Telegram.',
};

afterEach(() => {
    document.body.innerHTML = '';
    delete (window as unknown as Record<string, unknown>).Telegram;
});

describe('PremiumModal', () => {
    it('does not render when show is false', () => {
        mount(PremiumModal, {
            attachTo: document.body,
            props: { show: false, trialData: defaultTrialData },
        });
        expect(
            document.body.querySelector('[data-testid="premium-modal"]'),
        ).toBeNull();
    });

    it('renders modal when show is true', () => {
        mount(PremiumModal, {
            attachTo: document.body,
            props: { show: true, trialData: defaultTrialData },
        });
        expect(
            document.body.querySelector('[data-testid="premium-modal"]'),
        ).not.toBeNull();
    });

    it('displays features title and subtitle', () => {
        mount(PremiumModal, {
            attachTo: document.body,
            props: { show: true, trialData: defaultTrialData },
        });
        expect(document.body.textContent).toContain('Premium Features');
        expect(document.body.textContent).toContain(
            'Upgrade to unlock all features',
        );
    });

    it('displays all three feature items', () => {
        mount(PremiumModal, {
            attachTo: document.body,
            props: { show: true, trialData: defaultTrialData },
        });
        const list = document.body.querySelector(
            '[data-testid="premium-features-list"]',
        );
        expect(list?.textContent).toContain('Multiple reminders per habit');
        expect(list?.textContent).toContain('Daily AI digest of your progress');
        expect(list?.textContent).toContain('Export all your data as CSV');
    });

    it('displays upgrade button label', () => {
        mount(PremiumModal, {
            attachTo: document.body,
            props: { show: true, trialData: defaultTrialData },
        });
        const btn = document.body.querySelector(
            '[data-testid="premium-modal-upgrade"]',
        );
        expect(btn?.textContent?.trim()).toBe('Upgrade');
    });

    it('emits close when close button is clicked', async () => {
        const wrapper = mount(PremiumModal, {
            attachTo: document.body,
            props: { show: true, trialData: defaultTrialData },
        });
        const closeBtn = document.body.querySelector(
            '[data-testid="premium-modal-close"]',
        ) as HTMLElement;
        closeBtn.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        expect(wrapper.emitted('close')).toHaveLength(1);
    });

    it('emits close when backdrop is clicked', async () => {
        const wrapper = mount(PremiumModal, {
            attachTo: document.body,
            props: { show: true, trialData: defaultTrialData },
        });
        const backdrop = document.body.querySelector(
            '.fixed.inset-0.bg-black\\/50',
        ) as HTMLElement;
        backdrop.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        expect(wrapper.emitted('close')).toHaveLength(1);
    });

    it('opens external link when Telegram WebApp is unavailable', async () => {
        const openSpy = vi.spyOn(window, 'open').mockImplementation(() => null);

        mount(PremiumModal, {
            attachTo: document.body,
            props: { show: true, trialData: defaultTrialData },
        });

        const upgradeBtn = document.body.querySelector(
            '[data-testid="premium-modal-upgrade"]',
        ) as HTMLElement;
        upgradeBtn.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        await new Promise((r) => setTimeout(r, 0));

        expect(openSpy).toHaveBeenCalledWith('https://t.me/invoice', '_blank');
        expect(
            document.body.querySelector(
                '[data-testid="premium-modal-not-in-telegram"]',
            ),
        ).toBeNull();

        openSpy.mockRestore();
    });

    it('shows not-in-telegram message when invoiceLink is null', async () => {
        mount(PremiumModal, {
            attachTo: document.body,
            props: {
                show: true,
                trialData: { ...defaultTrialData, invoiceLink: null },
            },
        });

        const upgradeBtn = document.body.querySelector(
            '[data-testid="premium-modal-upgrade"]',
        ) as HTMLElement;
        upgradeBtn.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        await new Promise((r) => setTimeout(r, 0));

        expect(
            document.body.querySelector(
                '[data-testid="premium-modal-not-in-telegram"]',
            ),
        ).not.toBeNull();
    });

    it('emits close when Escape key is pressed', async () => {
        const wrapper = mount(PremiumModal, {
            attachTo: document.body,
            props: { show: true, trialData: defaultTrialData },
        });
        const dialog = document.body.querySelector(
            '[data-testid="premium-modal"]',
        ) as HTMLElement;
        dialog.dispatchEvent(
            new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }),
        );
        expect(wrapper.emitted('close')).toHaveLength(1);
    });

    it('calls tg.openInvoice when Telegram WebApp is available', async () => {
        const openInvoice = vi.fn();

        Object.defineProperty(window, 'Telegram', {
            writable: true,
            configurable: true,
            value: { WebApp: { openInvoice } },
        });

        mount(PremiumModal, {
            attachTo: document.body,
            props: { show: true, trialData: defaultTrialData },
        });

        const upgradeBtn = document.body.querySelector(
            '[data-testid="premium-modal-upgrade"]',
        ) as HTMLElement;
        upgradeBtn.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        await new Promise((r) => setTimeout(r, 0));

        expect(openInvoice).toHaveBeenCalledWith('https://t.me/invoice');
        expect(
            document.body.querySelector(
                '[data-testid="premium-modal-not-in-telegram"]',
            ),
        ).toBeNull();
    });
});
