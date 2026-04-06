import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';
import App from '@/App.vue';

vi.mock('@/pages/Dashboard.vue', () => ({
    default: {
        template:
            '<div data-testid="dashboard" @click="$emit(\'navigate\', \'track\')">Dashboard</div>',
        emits: ['navigate'],
    },
}));

vi.mock('@/pages/Track.vue', () => ({
    default: {
        template:
            '<div data-testid="track" @click="$emit(\'navigate\', \'dashboard\')">Track</div>',
        emits: ['navigate'],
    },
}));

describe('App', () => {
    it('renders Track by default', () => {
        const wrapper = mount(App);
        expect(wrapper.find('[data-testid="track"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="dashboard"]').exists()).toBe(false);
    });

    it('navigates to dashboard when Track emits navigate', async () => {
        const wrapper = mount(App);
        await wrapper.find('[data-testid="track"]').trigger('click');
        expect(wrapper.find('[data-testid="dashboard"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="track"]').exists()).toBe(false);
    });

    it('navigates back to track from dashboard', async () => {
        const wrapper = mount(App);
        await wrapper.find('[data-testid="track"]').trigger('click');
        expect(wrapper.find('[data-testid="dashboard"]').exists()).toBe(true);
        await wrapper.find('[data-testid="dashboard"]').trigger('click');
        expect(wrapper.find('[data-testid="track"]').exists()).toBe(true);
    });
});
