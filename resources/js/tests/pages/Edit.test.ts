import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import Edit from '@/pages/Edit.vue';
import {
    makeEditHabit,
    makeEditTranslations,
    makeHabitTranslations,
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
    templates: [] as ReturnType<typeof makeTemplateHabit>[],
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
    Element.prototype.animate = vi
        .fn()
        .mockReturnValue({ pause: vi.fn(), cancel: vi.fn() });
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

    it('inserts optimistic habit at correct sort_order position', async () => {
        const habit1 = makeEditHabit({ id: 1, name: 'First', sort_order: 1 });
        const habit2 = makeEditHabit({
            id: 2,
            name: 'Third',
            sort_order: 10,
        });
        const templates = [
            makeTemplateHabit({
                id: 10,
                name: 'Second',
                sort_order: 5,
            }),
        ];

        const wrapper = await mountEdit({
            ...defaultApiResponse,
            data: [habit1, habit2],
            templates,
        });

        const serverHabits = [
            habit1,
            makeEditHabit({ id: 50, name: 'Second', sort_order: 5 }),
            habit2,
        ];
        mockApiFetch.mockResolvedValueOnce({ data: serverHabits });

        const templateSection = wrapper.findComponent({
            name: 'TemplateSection',
        });
        templateSection.vm.$emit('copy', 10);
        await wrapper.vm.$nextTick();

        // Before server responds, optimistic habit is inserted between First and Third
        const items = wrapper.findAllComponents({ name: 'EditHabitItem' });
        expect(items[0].props('habit').name).toBe('First');
        expect(items[1].props('habit').name).toBe('Second');
        expect(items[2].props('habit').name).toBe('Third');

        await flushPromises();
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
});
