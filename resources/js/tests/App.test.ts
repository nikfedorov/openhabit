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
        emits: ['navigate', 'navigation-translations'],
    },
}));

vi.mock('@/pages/View.vue', () => ({
    default: {
        template:
            '<div data-testid="view" @click="$emit(\'navigate\', \'track\')">View</div>',
        emits: ['navigate', 'navigation-translations'],
    },
}));

describe('App', () => {
    it('renders Track by default', () => {
        const wrapper = mount(App);
        expect(wrapper.find('[data-testid="track"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="dashboard"]').exists()).toBe(false);
        expect(wrapper.find('[data-testid="view"]').exists()).toBe(false);
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

    it('navigates to view page via TabBar', async () => {
        const wrapper = mount(App);
        const tabButtons = wrapper.findAll('button');
        const viewTab = tabButtons.find((b) => b.text() === 'View');
        expect(viewTab).toBeTruthy();
        await viewTab!.trigger('click');
        expect(wrapper.find('[data-testid="view"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="track"]').exists()).toBe(false);
    });

    it('updates tab bar labels when navigation-translations is emitted', async () => {
        const wrapper = mount(App);
        const trackComponent = wrapper.findComponent({ name: 'Track' });
        trackComponent.vm.$emit('navigation-translations', {
            track: 'Трекер',
            view: 'Обзор',
        });
        await wrapper.vm.$nextTick();
        expect(wrapper.text()).toContain('Трекер');
        expect(wrapper.text()).toContain('Обзор');
    });
});
