import { mount } from '@vue/test-utils';
import { afterEach, describe, expect, it } from 'vitest';
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue';

afterEach(() => {
    document.body.innerHTML = '';
});

describe('DeleteConfirmModal', () => {
    it('renders nothing when show is false', () => {
        mount(DeleteConfirmModal, {
            attachTo: document.body,
            props: {
                show: false,
                title: 'Delete',
                message: 'Are you sure?',
                cancelLabel: 'Cancel',
                confirmLabel: 'Delete',
            },
        });
        expect(
            document.body.querySelector('[data-testid="delete-confirm-modal"]'),
        ).toBeNull();
    });

    it('renders modal when show is true', () => {
        mount(DeleteConfirmModal, {
            attachTo: document.body,
            props: {
                show: true,
                title: 'Delete Habit',
                message: 'Are you sure?',
                cancelLabel: 'Cancel',
                confirmLabel: 'Delete',
            },
        });
        expect(document.body.textContent).toContain('Delete Habit');
        expect(document.body.textContent).toContain('Are you sure?');
    });

    it('emits cancel when cancel button is clicked', async () => {
        const wrapper = mount(DeleteConfirmModal, {
            attachTo: document.body,
            props: {
                show: true,
                title: 'Delete',
                message: 'Sure?',
                cancelLabel: 'Cancel',
                confirmLabel: 'Delete',
            },
        });
        const cancelBtn = document.body.querySelector(
            '[data-testid="delete-cancel-btn"]',
        ) as HTMLElement;
        cancelBtn.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        expect(wrapper.emitted('cancel')).toHaveLength(1);
    });

    it('emits confirm when confirm button is clicked', async () => {
        const wrapper = mount(DeleteConfirmModal, {
            attachTo: document.body,
            props: {
                show: true,
                title: 'Delete',
                message: 'Sure?',
                cancelLabel: 'Cancel',
                confirmLabel: 'Delete',
            },
        });
        const confirmBtn = document.body.querySelector(
            '[data-testid="delete-confirm-btn"]',
        ) as HTMLElement;
        confirmBtn.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        expect(wrapper.emitted('confirm')).toHaveLength(1);
    });

    it('emits cancel when backdrop is clicked', async () => {
        const wrapper = mount(DeleteConfirmModal, {
            attachTo: document.body,
            props: {
                show: true,
                title: 'Delete',
                message: 'Sure?',
                cancelLabel: 'Cancel',
                confirmLabel: 'Delete',
            },
        });
        const backdrop = document.body.querySelector(
            '.fixed.inset-0.bg-black\\/40',
        ) as HTMLElement;
        backdrop.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        expect(wrapper.emitted('cancel')).toHaveLength(1);
    });

    it('displays custom labels', () => {
        mount(DeleteConfirmModal, {
            attachTo: document.body,
            props: {
                show: true,
                title: 'Remove Item',
                message: 'This cannot be undone.',
                cancelLabel: 'Go Back',
                confirmLabel: 'Yes, Remove',
            },
        });
        expect(document.body.textContent).toContain('Remove Item');
        expect(document.body.textContent).toContain('This cannot be undone.');
        expect(document.body.textContent).toContain('Go Back');
        expect(document.body.textContent).toContain('Yes, Remove');
    });
});
