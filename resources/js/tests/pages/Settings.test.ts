import { flushPromises } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import {
    defaultAiTones,
    makeSettingsResponse,
    mountSettings,
} from '@/tests/helpers/settings';

const { mockApiFetch } = vi.hoisted(() => ({
    mockApiFetch: vi.fn(),
}));

vi.mock('@/utils/api', () => ({
    apiFetch: mockApiFetch,
}));

const mockReload = vi.fn();

Object.defineProperty(window, 'location', {
    value: { reload: mockReload },
    writable: true,
});

beforeEach(() => {
    mockApiFetch.mockReset();
    mockReload.mockReset();
});

// ─── Loading ─────────────────────────────────────────────────

describe('Settings - Loading', () => {
    it('shows page loader while fetching, then renders content', async () => {
        let resolve!: (value: ReturnType<typeof makeSettingsResponse>) => void;
        const promise = new Promise<ReturnType<typeof makeSettingsResponse>>(
            (r) => (resolve = r),
        );
        mockApiFetch.mockReturnValueOnce(promise);

        const { mount } = await import('@vue/test-utils');
        const { default: Settings } = await import('@/pages/Settings.vue');
        const wrapper = mount(Settings);

        expect(wrapper.text()).toContain('');
        expect(wrapper.html()).toContain('page-loader');

        resolve(makeSettingsResponse());
        await flushPromises();

        expect(wrapper.html()).not.toContain('page-loader');
        expect(wrapper.text()).toContain('Settings');
    });

    it('emits navigation-translations, settings, and ready on load', async () => {
        const { mount } = await import('@vue/test-utils');
        const { default: Settings } = await import('@/pages/Settings.vue');
        mockApiFetch.mockResolvedValueOnce(makeSettingsResponse());
        const wrapper = mount(Settings);
        await flushPromises();

        expect(wrapper.emitted('navigation-translations')).toBeTruthy();
        expect(wrapper.emitted('settings')).toBeTruthy();
        expect(wrapper.emitted('ready')).toBeTruthy();
    });
});

// ─── Appearance ───────────────────────────────────────────────

describe('Settings - Appearance', () => {
    it('renders all three theme buttons', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        expect(wrapper.find('[title="Light"]').exists()).toBe(true);
        expect(wrapper.find('[title="Dark"]').exists()).toBe(true);
        expect(wrapper.find('[title="System"]').exists()).toBe(true);
    });

    it('applies active class to the dark theme button', async () => {
        const wrapper = await mountSettings(mockApiFetch, { theme: 'dark' });
        const darkBtn = wrapper.find('[title="Dark"]');
        expect(darkBtn.classes()).toContain('bg-green-100');
    });

    it('applies active class to the system theme button', async () => {
        const wrapper = await mountSettings(mockApiFetch, { theme: 'system' });
        const systemBtn = wrapper.find('[title="System"]');
        expect(systemBtn.classes()).toContain('bg-green-100');
    });

    it('applies active class to the light theme button', async () => {
        const wrapper = await mountSettings(mockApiFetch, { theme: 'light' });
        const lightBtn = wrapper.find('[title="Light"]');
        expect(lightBtn.classes()).toContain('bg-green-100');
    });

    it('calls PATCH when theme is changed', async () => {
        const wrapper = await mountSettings(mockApiFetch, { theme: 'system' });
        mockApiFetch.mockResolvedValueOnce({ data: { theme: 'light' } });

        await wrapper.find('[title="Light"]').trigger('click');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                method: 'PATCH',
                body: expect.stringContaining('"theme":"light"'),
            }),
            expect.anything(),
        );
    });

    it('calls PATCH when dark theme is selected', async () => {
        const wrapper = await mountSettings(mockApiFetch, { theme: 'system' });
        mockApiFetch.mockResolvedValueOnce({ data: { theme: 'dark' } });

        await wrapper.find('[title="Dark"]').trigger('click');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"theme":"dark"'),
            }),
            expect.anything(),
        );
    });

    it('calls PATCH when system theme is selected', async () => {
        const wrapper = await mountSettings(mockApiFetch, { theme: 'light' });
        mockApiFetch.mockResolvedValueOnce({ data: { theme: 'system' } });

        await wrapper.find('[title="System"]').trigger('click');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"theme":"system"'),
            }),
            expect.anything(),
        );
    });
});

// ─── Language & Region ────────────────────────────────────────

describe('Settings - Language & Region', () => {
    it('renders current locale label in the button', async () => {
        const wrapper = await mountSettings(mockApiFetch, { locale: 'en' });
        expect(wrapper.text()).toContain('English');
    });

    it('falls back to raw locale code when locale not in locales map', async () => {
        // locale 'fr' is not in the test locales map
        const wrapper = await mountSettings(mockApiFetch, { locale: 'fr' });
        expect(wrapper.text()).toContain('fr');
    });

    it('opens locale dropdown on button click', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        expect(wrapper.text()).not.toContain('Russian');

        const localeBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('English'))!;
        await localeBtn.trigger('click');

        expect(wrapper.text()).toContain('Russian');
    });

    it('closes locale dropdown via backdrop click', async () => {
        const wrapper = await mountSettings(mockApiFetch);

        const localeBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('English'))!;
        await localeBtn.trigger('click');
        expect(wrapper.text()).toContain('Russian');

        // Click backdrop
        const backdrop = wrapper.findAll('.fixed.inset-0')[0]!;
        await backdrop.trigger('click');
        expect(wrapper.text()).not.toContain('Russian');
    });

    it('selects a locale and triggers reload', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        mockApiFetch.mockResolvedValueOnce({ data: { locale: 'ru' } });

        const localeBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('English'))!;
        await localeBtn.trigger('click');

        const ruBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Russian')!;
        await ruBtn.trigger('click');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"locale":"ru"'),
            }),
            expect.anything(),
        );
        expect(mockReload).toHaveBeenCalledOnce();
    });

    it('falls back to UTC when timezone is null in response', async () => {
        const wrapper = await mountSettings(mockApiFetch, { timezone: null });
        // UTC is not in the test timezone map, so it shows raw 'UTC'
        expect(wrapper.text()).toContain('UTC');
    });

    it('shows active class on currently-selected timezone in dropdown', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            timezone: 'America/New_York',
        });
        // Find the timezone trigger button (it shows the current timezone label)
        const tzBtn = wrapper
            .findAll('button')
            .find(
                (b) =>
                    b.text().includes('New York') &&
                    b.classes().some((c) => c.includes('max-w')),
            )!;
        await tzBtn.trigger('click');

        // The active option button inside the dropdown has bg-green-50
        const activeOptions = wrapper
            .findAll('button')
            .filter((b) => b.classes().includes('bg-green-50'));
        expect(activeOptions.length).toBeGreaterThan(0);
        expect(activeOptions[0].text()).toContain('New York');
    });

    it('renders current timezone label in the button', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            timezone: 'America/New_York',
        });
        expect(wrapper.text()).toContain('New York');
    });

    it('opens timezone dropdown on button click', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        const tzBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('UTC'))!;
        await tzBtn.trigger('click');
        expect(wrapper.text()).toContain('America');
        expect(wrapper.text()).toContain('Europe');
    });

    it('closes timezone dropdown via backdrop click', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        const tzBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('UTC'))!;
        await tzBtn.trigger('click');
        expect(wrapper.text()).toContain('America');

        const backdrop = wrapper.findAll('.fixed.inset-0')[0]!;
        await backdrop.trigger('click');
        expect(wrapper.text()).not.toContain('America');
    });

    it('filters timezones by search query', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        const tzBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('UTC'))!;
        await tzBtn.trigger('click');

        const searchInput = wrapper.find(
            'input[placeholder="Search timezone..."]',
        );
        await searchInput.setValue('London');
        await searchInput.trigger('input');

        expect(wrapper.text()).toContain('London');
        expect(wrapper.text()).not.toContain('New York');
    });

    it('shows "No timezones found" when search yields no results', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        const tzBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('UTC'))!;
        await tzBtn.trigger('click');

        const searchInput = wrapper.find(
            'input[placeholder="Search timezone..."]',
        );
        await searchInput.setValue('xxxxxxxxxxx');
        await searchInput.trigger('input');

        expect(wrapper.text()).toContain('No timezones found');
    });

    it('closes timezone dropdown on Escape key', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        const tzBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('UTC'))!;
        await tzBtn.trigger('click');
        expect(wrapper.text()).toContain('America');

        const searchInput = wrapper.find(
            'input[placeholder="Search timezone..."]',
        );
        await searchInput.trigger('keydown.escape');

        expect(wrapper.text()).not.toContain('America');
    });

    it('selects a timezone and saves', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        mockApiFetch.mockResolvedValueOnce({
            data: { timezone: 'Europe/London' },
        });

        const tzBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('UTC'))!;
        await tzBtn.trigger('click');

        const londonBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'London')!;
        await londonBtn.trigger('click');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"timezone":"Europe/London"'),
            }),
            expect.anything(),
        );
        // Dropdown should close
        expect(wrapper.text()).not.toContain('America');
    });

    it('falls back to raw key when timezone not in list', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            timezone: 'Unknown/Zone',
        });
        expect(wrapper.text()).toContain('Unknown/Zone');
    });
});

// ─── Personal ─────────────────────────────────────────────────

describe('Settings - Personal', () => {
    it('renders the move-completed toggle in correct state', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            moveCompletedToEnd: false,
        });
        const toggle = wrapper.find('[role="switch"]');
        expect(toggle.attributes('aria-checked')).toBe('false');
    });

    it('toggles moveCompletedToEnd and saves', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            moveCompletedToEnd: false,
        });
        mockApiFetch.mockResolvedValueOnce({
            data: { moveCompletedToEnd: true },
        });

        await wrapper.find('[role="switch"]').trigger('click');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"moveCompletedToEnd":true'),
            }),
            expect.anything(),
        );
    });

    it('renders birthdate as DD.MM.YYYY when set', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            birthdate: '1990-05-20',
        });
        const input = wrapper.find('input[placeholder="DD.MM.YYYY"]');
        expect((input.element as HTMLInputElement).value).toBe('20.05.1990');
    });

    it('saves valid birthdate on blur', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        mockApiFetch.mockResolvedValueOnce({
            data: { birthdate: '1995-12-31' },
        });

        const input = wrapper.find('input[placeholder="DD.MM.YYYY"]');
        await input.setValue('31.12.1995');
        await input.trigger('blur');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"birthdate":"1995-12-31"'),
            }),
            expect.anything(),
        );
    });

    it('shows error state for invalid birthdate format', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        const input = wrapper.find('input[placeholder="DD.MM.YYYY"]');
        // With the date mask, only digits are accepted; use a date with invalid month (13)
        await input.setValue('13.13.2000');
        await input.trigger('blur');
        await flushPromises();

        expect(input.classes()).toContain('text-red-500');
        // Should not call PATCH
        expect(mockApiFetch).toHaveBeenCalledTimes(1); // only the initial GET
    });

    it('shows error state for invalid calendar date (e.g. Feb 30)', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        const input = wrapper.find('input[placeholder="DD.MM.YYYY"]');
        await input.setValue('30.02.2000');
        await input.trigger('blur');
        await flushPromises();

        expect(input.classes()).toContain('text-red-500');
        expect(mockApiFetch).toHaveBeenCalledTimes(1);
    });

    it('shows error state for invalid calendar date', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        const input = wrapper.find('input[placeholder="DD.MM.YYYY"]');
        await input.setValue('32.01.2000');
        await input.trigger('blur');
        await flushPromises();

        expect(input.classes()).toContain('text-red-500');
    });

    it('shows error state when birthdate format does not match DD.MM.YYYY', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        const input = wrapper.find('input[placeholder="DD.MM.YYYY"]');
        // Single-digit day/month does not match the ^(\d{2})\.(\d{2})\.(\d{4})$ regex
        await input.setValue('1.1.2020');
        await input.trigger('blur');
        await flushPromises();

        expect(input.classes()).toContain('text-red-500');
        expect(mockApiFetch).toHaveBeenCalledTimes(1); // only initial GET
    });

    it('triggers blur on birthdate input when Enter is pressed', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            birthdate: '1990-05-20',
        });
        mockApiFetch.mockResolvedValueOnce({
            data: { birthdate: '1990-05-20' },
        });

        const input = wrapper.find('input[placeholder="DD.MM.YYYY"]');
        const blurSpy = vi.spyOn(input.element as HTMLInputElement, 'blur');
        await input.trigger('keydown.enter');

        expect(blurSpy).toHaveBeenCalled();
    });

    it('saves dayStartsAt when minute selected from picker', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        mockApiFetch.mockResolvedValueOnce({ data: { dayStartsAt: '03:30' } });

        // Open the time picker
        const pickerBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('03') && b.text().includes('00'))!;
        await pickerBtn.trigger('click');

        // Select minute 30 from the minutes column
        const minuteColumn = wrapper.findAll('.overflow-y-auto')[1];
        const min30 = minuteColumn
            .findAll('button')
            .find((b) => b.text() === '30')!;
        await min30.trigger('click');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"dayStartsAt":"03:30"'),
            }),
            expect.anything(),
        );
    });

    it('clears birthdate when field is empty', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            birthdate: '1990-01-01',
        });
        mockApiFetch.mockResolvedValueOnce({ data: { birthdate: null } });

        const input = wrapper.find('input[placeholder="DD.MM.YYYY"]');
        await input.setValue('');
        await input.trigger('blur');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"birthdate":null'),
            }),
            expect.anything(),
        );
    });

    it('falls back to 03:00 when dayStartsAt is null in response', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            dayStartsAt: null,
        });
        const pickerBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('03'))!;
        expect(pickerBtn.exists()).toBe(true);
    });

    it('saves valid dayStartsAt via updateDayStartsAt', async () => {
        const wrapper = await mountSettings(mockApiFetch);
        mockApiFetch.mockResolvedValueOnce({ data: { dayStartsAt: '06:00' } });

        const vm = wrapper.vm as any;
        await vm.$.setupState.updateDayStartsAt('06:00');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"dayStartsAt":"06:00"'),
            }),
            expect.anything(),
        );
    });

    it('does not save invalid dayStartsAt format', async () => {
        const wrapper = await mountSettings(mockApiFetch);

        const vm = wrapper.vm as any;
        await vm.$.setupState.updateDayStartsAt('99:99');
        await flushPromises();

        // Only the initial GET call
        expect(mockApiFetch).toHaveBeenCalledTimes(1);
    });

    it('falls back to current dayStartsAt when called without argument', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            dayStartsAt: '04:15',
        });
        mockApiFetch.mockResolvedValueOnce({ data: { dayStartsAt: '04:15' } });

        const vm = wrapper.vm as any;
        await vm.$.setupState.updateDayStartsAt();
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"dayStartsAt":"04:15"'),
            }),
            expect.anything(),
        );
    });
});

// ─── AI Digest ────────────────────────────────────────────────

describe('Settings - AI Digest', () => {
    it('shows AI digest section collapsed when disabled', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            aiDigestTime: null,
        });
        expect(wrapper.text()).not.toContain('Digest time');
    });

    it('shows digest time and tone list when enabled', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            aiDigestTime: '08:00',
        });
        expect(wrapper.text()).toContain('Digest time');
        expect(wrapper.text()).toContain('AI tone');
    });

    it('enables AI digest and saves time when premium user toggles on', async () => {
        const wrapper = await mountSettings(
            mockApiFetch,
            { aiDigestTime: null },
            { hasPremium: true },
        );
        mockApiFetch.mockResolvedValueOnce({ data: { aiDigestTime: '09:00' } });

        const toggle = wrapper.findAll('[role="switch"]')[1]!;
        await toggle.trigger('click');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"aiDigestTime"'),
            }),
            expect.anything(),
        );
    });

    it('disables AI digest immediately for non-premium user', async () => {
        const wrapper = await mountSettings(
            mockApiFetch,
            { aiDigestTime: null },
            { hasPremium: false },
        );
        const toggle = wrapper.findAll('[role="switch"]')[1]!;
        await toggle.trigger('click');
        await flushPromises();

        // Should not call PATCH, premium guard reverts toggle
        expect(mockApiFetch).toHaveBeenCalledTimes(1); // only GET
        expect(wrapper.text()).not.toContain('Digest time');
    });

    it('disables AI digest and saves null when toggled off', async () => {
        const wrapper = await mountSettings(
            mockApiFetch,
            { aiDigestTime: '08:00' },
            { hasPremium: true },
        );
        mockApiFetch.mockResolvedValueOnce({ data: { aiDigestTime: null } });

        const toggle = wrapper.findAll('[role="switch"]')[1]!;
        await toggle.trigger('click');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"aiDigestTime":null'),
            }),
            expect.anything(),
        );
    });

    it('saves digest time when minute selected from picker', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            aiDigestTime: '08:00',
        });
        mockApiFetch.mockResolvedValueOnce({ data: { aiDigestTime: '08:30' } });

        // Open the time picker
        const pickerBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('08') && b.text().includes('00'))!;
        await pickerBtn.trigger('click');

        // Select minute 30 from the minutes column
        const minuteColumn = wrapper.findAll('.overflow-y-auto')[1];
        const min30 = minuteColumn
            .findAll('button')
            .find((b) => b.text() === '30')!;
        await min30.trigger('click');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"aiDigestTime":"08:30"'),
            }),
            expect.anything(),
        );
    });

    it('does not save invalid aiDigestTime format', async () => {
        const wrapper = await mountSettings(
            mockApiFetch,
            { aiDigestTime: '08:00' },
            { hasPremium: true },
        );

        // Call the internal function directly with an invalid time
        // to cover the regex validation guard
        const vm = wrapper.vm as any;
        await vm.$.setupState.updateAiDigestTime('invalid');
        await flushPromises();

        // Only the initial GET call — no PATCH for invalid time
        expect(mockApiFetch).toHaveBeenCalledTimes(1);
    });

    it('uses current aiDigestTime when called without argument', async () => {
        const wrapper = await mountSettings(
            mockApiFetch,
            { aiDigestTime: '08:00' },
            { hasPremium: true },
        );
        mockApiFetch.mockResolvedValueOnce({ data: { aiDigestTime: '08:00' } });

        // Call without argument to cover the ?? fallback branch
        const vm = wrapper.vm as any;
        await vm.$.setupState.updateAiDigestTime();
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"aiDigestTime":"08:00"'),
            }),
            expect.anything(),
        );
    });

    it('renders fallback icon for unknown AI tone icon', async () => {
        const tonesWithUnknown = [
            ...defaultAiTones,
            { id: 5, name: 'Custom', description: null, icon: 'unknown-icon' },
        ];
        const wrapper = await mountSettings(
            mockApiFetch,
            { aiDigestTime: '08:00' },
            { aiTones: tonesWithUnknown },
        );
        expect(wrapper.text()).toContain('Custom');
    });

    it('renders all AI tone options', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            aiDigestTime: '08:00',
        });
        for (const tone of defaultAiTones) {
            expect(wrapper.text()).toContain(tone.name);
        }
    });

    it('applies active class to selected AI tone', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            aiDigestTime: '08:00',
            aiToneId: 1,
        });

        const toneButtons = wrapper
            .findAll('button')
            .filter((b) =>
                defaultAiTones.some((t) => b.text().includes(t.name)),
            );

        const activeBtn = toneButtons.find((b) =>
            b.text().includes('Motivational'),
        )!;
        expect(activeBtn.classes()).toContain('bg-green-50');
    });

    it('saves selected AI tone on click', async () => {
        const wrapper = await mountSettings(mockApiFetch, {
            aiDigestTime: '08:00',
            aiToneId: 1,
        });
        mockApiFetch.mockResolvedValueOnce({ data: { aiToneId: 2 } });

        const calmBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('Calm'))!;
        await calmBtn.trigger('click');
        await flushPromises();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings',
            expect.objectContaining({
                body: expect.stringContaining('"aiToneId":2'),
            }),
            expect.anything(),
        );
    });

    it('does not render AI tone section when aiTones list is empty', async () => {
        const wrapper = await mountSettings(
            mockApiFetch,
            { aiDigestTime: '08:00' },
            { aiTones: [] },
        );
        expect(wrapper.text()).not.toContain('AI tone');
    });
});
