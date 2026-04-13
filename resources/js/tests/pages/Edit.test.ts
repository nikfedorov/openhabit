import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import Edit from '@/pages/Edit.vue';
import {
    makeEditHabit,
    makeEditTranslations,
    makeHabitTranslations,
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

vi.mock('vue-draggable-plus', () => ({
    VueDraggable: {
        name: 'VueDraggable',
        props: {
            modelValue: { type: Array, default: () => [] },
            animation: { type: Number, default: 0 },
            handle: { type: String, default: '' },
            ghostClass: { type: String, default: '' },
            dragClass: { type: String, default: '' },
        },
        emits: ['update:modelValue', 'end'],
        template: '<div><slot /></div>',
    },
}));

const defaultApiResponse = {
    data: [makeEditHabit({ id: 1, name: 'Run' })],
    translations: makeEditTranslations(),
    habitTranslations: makeHabitTranslations(),
    navigationTranslations: { track: 'Track', view: 'View', edit: 'Edit' },
    settings: { locale: 'en', theme: 'system', moveCompletedToEnd: true },
};

async function mountEdit(apiResponse = defaultApiResponse) {
    mockApiFetch.mockResolvedValueOnce(apiResponse);
    const wrapper = mount(Edit);
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

    it('toggles franklin habit active state', async () => {
        const wrapper = await mountEdit({
            ...defaultApiResponse,
            data: [
                ...defaultApiResponse.data,
                makeEditHabit({
                    id: 99,
                    name: 'Temperance',
                    is_franklin_virtue: true,
                }),
            ],
        });
        mockApiFetch.mockResolvedValueOnce(undefined);
        const franklinSection = wrapper.findComponent({
            name: 'FranklinSection',
        });
        franklinSection.vm.$emit('toggle-active', 99);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/edit/habits/99/toggle',
            { method: 'POST' },
            { silent: true },
        );
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

    it('saves reorder when drag ends', async () => {
        const habit1 = makeEditHabit({ id: 1, name: 'Run' });
        const habit2 = makeEditHabit({ id: 2, name: 'Read' });
        const wrapper = await mountEdit({
            ...defaultApiResponse,
            data: [habit1, habit2],
        });
        mockApiFetch.mockResolvedValueOnce(undefined);
        const draggable = wrapper.findComponent({ name: 'VueDraggable' });
        draggable.vm.$emit('update:modelValue', [habit2, habit1]);
        draggable.vm.$emit('end');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/edit/habits/reorder',
            {
                method: 'POST',
                body: JSON.stringify({ ordered_ids: [2, 1] }),
            },
            { silent: true },
        );
    });
});
