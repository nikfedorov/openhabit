import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import Edit from '@/pages/Edit.vue';
import {
    makeEditHabit,
    makeEditTranslations,
    makeTemplateHabit,
} from '@/tests/helpers/edit';

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
    settings: { locale: 'en', theme: 'system', moveCompletedToEnd: true },
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
});
