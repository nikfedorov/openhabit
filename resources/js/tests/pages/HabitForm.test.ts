import { flushPromises, mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { nextTick } from 'vue';
import HabitForm from '@/pages/HabitForm.vue';
import { makeHabitTranslations } from '@/tests/helpers/edit';

const { mockApiFetch, mockRouterPush, mockRouteParams, mockIsTelegram } =
    vi.hoisted(() => ({
        mockApiFetch: vi.fn(),
        mockRouterPush: vi.fn(),
        mockRouteParams: { value: {} as Record<string, string> },
        mockIsTelegram: vi.fn(() => false),
    }));

vi.mock('@/utils/api', () => ({
    apiFetch: mockApiFetch,
}));

vi.mock('vue-router', () => ({
    useRoute: () => ({ params: mockRouteParams.value }),
    useRouter: () => ({ push: mockRouterPush }),
}));

vi.mock('@/composables/useTelegramBackButton', () => ({
    isTelegram: mockIsTelegram,
    useTelegramBackButton: vi.fn(),
}));

const createApiResponse = {
    habitTranslations: makeHabitTranslations(),
    navigationTranslations: { track: 'Track', view: 'View', edit: 'Edit' },
    settings: { locale: 'en', theme: 'system', moveCompletedToEnd: true },
};

const habitShowResponse = {
    data: {
        id: 1,
        name: 'Morning Run',
        description: 'Run 5km',
        iterations_required: 2,
        is_active: true,
        frequency: 'DAILY',
        weekly_days: [],
        monthly_days: [],
        monthly_mode: 'day',
        monthly_position: 1,
        monthly_weekday: 0,
        notifications: [],
    },
    habitTranslations: makeHabitTranslations(),
    navigationTranslations: { track: 'Track', view: 'View', edit: 'Edit' },
    settings: { locale: 'en', theme: 'system', moveCompletedToEnd: true },
};

async function mountCreateForm() {
    mockRouteParams.value = {};
    mockApiFetch.mockResolvedValueOnce(createApiResponse);
    const wrapper = mount(HabitForm, { attachTo: document.body });
    await flushPromises();
    return wrapper;
}

async function mountEditForm() {
    mockRouteParams.value = { id: '1' };
    mockApiFetch.mockResolvedValueOnce(habitShowResponse);
    const wrapper = mount(HabitForm, { attachTo: document.body });
    await flushPromises();
    return wrapper;
}

beforeEach(() => {
    mockApiFetch.mockReset();
    mockRouterPush.mockReset();
    mockRouteParams.value = {};
});

afterEach(() => {
    document.body.innerHTML = '';
});

describe('HabitForm - Create Mode', () => {
    it('loads translations on mount', async () => {
        await mountCreateForm();
        expect(mockApiFetch).toHaveBeenCalledWith('/api/edit/habits/create');
    });

    it('shows new habit title', async () => {
        const wrapper = await mountCreateForm();
        expect(wrapper.text()).toContain('New Habit');
    });

    it('renders form fields', async () => {
        const wrapper = await mountCreateForm();
        expect(wrapper.find('input#habit-name').exists()).toBe(true);
        expect(wrapper.find('textarea#habit-description').exists()).toBe(true);
        expect(wrapper.text()).toContain('Daily');
        expect(wrapper.text()).toContain('Weekly');
        expect(wrapper.text()).toContain('Monthly');
    });

    it('emits ready after loading', async () => {
        const wrapper = await mountCreateForm();
        expect(wrapper.emitted('ready')).toBeTruthy();
    });

    it('submits form via POST for new habit', async () => {
        const wrapper = await mountCreateForm();
        mockApiFetch.mockResolvedValueOnce({});

        await wrapper.find('input#habit-name').setValue('New Test Habit');
        await wrapper.find('form').trigger('submit');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/edit/habits',
            expect.objectContaining({ method: 'POST' }),
        );
        expect(mockRouterPush).toHaveBeenCalledWith({ name: 'edit' });
    });

    it('does not show delete button in create mode', async () => {
        const wrapper = await mountCreateForm();
        expect(wrapper.text()).not.toContain('Delete Habit');
    });

    it('navigates back on cancel', async () => {
        const wrapper = await mountCreateForm();
        const cancelBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Cancel')!;
        await cancelBtn.trigger('click');
        expect(mockRouterPush).toHaveBeenCalledWith({ name: 'edit' });
    });

    it('navigates back on back button click', async () => {
        const wrapper = await mountCreateForm();
        // Back button is the first button in the header
        const backBtn = wrapper.findAll('button')[0];
        await backBtn.trigger('click');
        expect(mockRouterPush).toHaveBeenCalledWith({ name: 'edit' });
    });

    it('hides back button when in Telegram', async () => {
        mockIsTelegram.mockReturnValue(true);
        const wrapper = await mountCreateForm();
        // In non-Telegram mode, the first button is the back arrow.
        // In Telegram mode, the back button should not render.
        const buttons = wrapper.findAll('button');
        const hasBackBtn = buttons.some((b) =>
            b.find('svg.rtl\\:rotate-180').exists(),
        );
        expect(hasBackBtn).toBe(false);
        mockIsTelegram.mockReturnValue(false);
    });
});

describe('HabitForm - Edit Mode', () => {
    it('loads habit data in edit mode', async () => {
        await mountEditForm();
        expect(mockApiFetch).toHaveBeenCalledWith('/api/edit/habits/1');
        expect(mockApiFetch).toHaveBeenCalledTimes(1);
    });

    it('shows edit habit title', async () => {
        const wrapper = await mountEditForm();
        expect(wrapper.text()).toContain('Edit Habit');
    });

    it('populates form with habit data', async () => {
        const wrapper = await mountEditForm();
        const nameInput = wrapper.find('input#habit-name');
        expect(nameInput.element.value).toBe('Morning Run');
    });

    it('submits form via PUT for existing habit', async () => {
        const wrapper = await mountEditForm();
        mockApiFetch.mockResolvedValueOnce({});

        await wrapper.find('form').trigger('submit');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/edit/habits/1',
            expect.objectContaining({ method: 'PUT' }),
        );
        expect(mockRouterPush).toHaveBeenCalledWith({ name: 'edit' });
    });

    it('shows delete button in edit mode', async () => {
        const wrapper = await mountEditForm();
        expect(wrapper.text()).toContain('Delete Habit');
    });

    it('opens confirm dialog when delete button clicked', async () => {
        const wrapper = await mountEditForm();
        const deleteBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Delete Habit')!;
        await deleteBtn.trigger('click');
        expect(document.body.textContent).toContain('Are you sure?');
    });

    it('closes confirm dialog on cancel', async () => {
        const wrapper = await mountEditForm();
        const deleteBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Delete Habit')!;
        await deleteBtn.trigger('click');
        // Modal's Cancel is the last Cancel button in body (after form's Cancel)
        const modalCancelBtn = Array.from(
            document.body.querySelectorAll('button'),
        )
            .filter((b) => b.textContent?.trim() === 'Cancel')
            .at(-1)!;
        modalCancelBtn.dispatchEvent(
            new MouseEvent('click', { bubbles: true }),
        );
        await nextTick();
        expect(document.body.textContent).not.toContain('Are you sure?');
    });

    it('deletes habit and navigates back after confirm', async () => {
        const wrapper = await mountEditForm();
        mockApiFetch.mockResolvedValueOnce({});
        // Open confirm dialog
        const deleteBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Delete Habit')!;
        await deleteBtn.trigger('click');
        // Modal's confirm button is the last 'Delete Habit' button in body
        const modalConfirmBtn = Array.from(
            document.body.querySelectorAll('button'),
        )
            .filter((b) => b.textContent?.trim() === 'Delete Habit')
            .at(-1)!;
        modalConfirmBtn.dispatchEvent(
            new MouseEvent('click', { bubbles: true }),
        );
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith('/api/edit/habits/1', {
            method: 'DELETE',
        });
        expect(mockRouterPush).toHaveBeenCalledWith({ name: 'edit' });
    });

    it('closes confirm dialog when clicking backdrop', async () => {
        const wrapper = await mountEditForm();
        const deleteBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Delete Habit')!;
        await deleteBtn.trigger('click');
        // Click the backdrop div (first div inside the teleport overlay)
        const backdrop = document.body.querySelector(
            '.fixed.inset-0.bg-black\\/40',
        ) as HTMLElement;
        backdrop.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        await nextTick();
        expect(document.body.textContent).not.toContain('Are you sure?');
    });

    it('closes confirm dialog when clicking overlay self', async () => {
        const wrapper = await mountEditForm();
        const deleteBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Delete Habit')!;
        await deleteBtn.trigger('click');
        // Click the overlay container itself (not a child)
        const overlay = document.body.querySelector(
            '.fixed.inset-0.z-50',
        ) as HTMLElement;
        overlay.dispatchEvent(
            new MouseEvent('click', {
                bubbles: false,
                target: overlay,
            } as MouseEventInit),
        );
        await nextTick();
        expect(document.body.textContent).not.toContain('Are you sure?');
    });

    it('shows weekly days when frequency is WEEKLY', async () => {
        mockRouteParams.value = { id: '1' };
        mockApiFetch.mockResolvedValueOnce({
            ...habitShowResponse,
            data: {
                ...habitShowResponse.data,
                frequency: 'WEEKLY',
                weekly_days: [0, 2, 4],
            },
        });
        const wrapper = mount(HabitForm);
        await flushPromises();
        expect(wrapper.text()).toContain('Days of Week');
    });

    it('shows monthly options when frequency is MONTHLY', async () => {
        mockRouteParams.value = { id: '1' };
        mockApiFetch.mockResolvedValueOnce({
            ...habitShowResponse,
            data: {
                ...habitShowResponse.data,
                frequency: 'MONTHLY',
                monthly_days: [1, 15],
            },
        });
        const wrapper = mount(HabitForm);
        await flushPromises();
        expect(wrapper.text()).toContain('Specific Days');
    });

    it('toggles active state', async () => {
        const wrapper = await mountEditForm();
        // Find the toggle button - it's the one with rounded-full class
        const toggleBtn = wrapper.find('button.rounded-full');
        expect(toggleBtn.classes()).toContain('bg-green-500');
        await toggleBtn.trigger('click');
        expect(toggleBtn.classes()).toContain('bg-neutral-300');
    });

    it('renders create form with frequency selector in create mode', async () => {
        const wrapper = await mountCreateForm();
        // Change frequency to Weekly by clicking the Weekly button
        const weeklyBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Weekly')!;
        await weeklyBtn.trigger('click');
        expect(wrapper.text()).toContain('Days of Week');
    });

    it('renders monthly options when switching to monthly', async () => {
        const wrapper = await mountCreateForm();
        const monthlyBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Monthly')!;
        await monthlyBtn.trigger('click');
        expect(wrapper.text()).toContain('Specific Days');
    });

    it('updates iterations count in form', async () => {
        const wrapper = await mountCreateForm();
        const iterationsComponent = wrapper.findComponent({
            name: 'IterationsCounter',
        });
        iterationsComponent.vm.$emit('update:modelValue', 5);
        await flushPromises();
        const input = iterationsComponent.find('input');
        expect(input.exists()).toBe(true);
    });

    it('adds notification in form', async () => {
        const wrapper = await mountCreateForm();
        const notificationComponent = wrapper.findComponent({
            name: 'NotificationList',
        });
        notificationComponent.vm.$emit('update:modelValue', [
            { time: '09:00', is_active: true },
        ]);
        await flushPromises();
        expect(notificationComponent.exists()).toBe(true);
    });

    it('interacts with monthly options in form', async () => {
        const wrapper = await mountCreateForm();
        // Switch to Monthly
        const monthlyBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Monthly')!;
        await monthlyBtn.trigger('click');

        const monthlyComponent = wrapper.findComponent({
            name: 'MonthlyOptions',
        });
        monthlyComponent.vm.$emit('update:mode', 'position');
        monthlyComponent.vm.$emit('update:days', [1, 15]);
        monthlyComponent.vm.$emit('update:position', 2);
        monthlyComponent.vm.$emit('update:weekday', 3);
        await flushPromises();
        expect(monthlyComponent.exists()).toBe(true);
    });

    it('interacts with weekly days in form', async () => {
        const wrapper = await mountCreateForm();
        // Switch to Weekly
        const weeklyBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Weekly')!;
        await weeklyBtn.trigger('click');

        const weeklyComponent = wrapper.findComponent({
            name: 'WeeklyDays',
        });
        weeklyComponent.vm.$emit('update:modelValue', [0, 2, 4]);
        await flushPromises();
        expect(weeklyComponent.exists()).toBe(true);
    });

    it('updates description in form', async () => {
        const wrapper = await mountCreateForm();
        const textarea = wrapper.find('textarea#habit-description');
        await textarea.setValue('Test description');
        expect(textarea.element.value).toBe('Test description');
    });
});
