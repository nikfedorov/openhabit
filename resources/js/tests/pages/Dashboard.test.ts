import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';
import Dashboard from '../../pages/Dashboard.vue';

const mockUser = {
    id: '1',
    name: 'Test User',
    email: 'test@example.com',
    telegram_id: '123456',
    locale: 'en',
    last_active_at: '2025-04-01 12:00:00',
    created_at: '2025-01-01 00:00:00',
    updated_at: '2025-01-01 00:00:00',
};

const defaultPageProps = {
    auth: { user: mockUser },
    name: 'OpenHabit',
};

vi.mock('@inertiajs/vue3', () => ({
    Head: { template: '<div><slot /></div>' },
    usePage: vi.fn(() => ({
        props: defaultPageProps,
    })),
}));

function mountDashboard(pageProps = defaultPageProps) {
    return mount(Dashboard, {
        global: {
            config: {
                globalProperties: {
                    $page: { props: pageProps },
                },
            },
        },
    });
}

describe('Dashboard', () => {
    it('renders the application name as heading', () => {
        const wrapper = mountDashboard();
        expect(wrapper.find('h1').text()).toBe('OpenHabit');
    });

    it('renders the user information section', () => {
        const wrapper = mountDashboard();
        expect(wrapper.find('h2').text()).toBe('User Information');
    });

    it('displays user name', () => {
        const wrapper = mountDashboard();
        expect(wrapper.text()).toContain('Test User');
    });

    it('displays user telegram id', () => {
        const wrapper = mountDashboard();
        expect(wrapper.text()).toContain('123456');
    });

    it('displays user email', () => {
        const wrapper = mountDashboard();
        expect(wrapper.text()).toContain('test@example.com');
    });

    it('displays user locale', () => {
        const wrapper = mountDashboard();
        expect(wrapper.text()).toContain('en');
    });

    it('displays last active date', () => {
        const wrapper = mountDashboard();
        expect(wrapper.text()).toContain('2025-04-01 12:00:00');
    });

    it('displays member since date', () => {
        const wrapper = mountDashboard();
        expect(wrapper.text()).toContain('2025-01-01 00:00:00');
    });

    it('displays all field labels', () => {
        const wrapper = mountDashboard();
        const labels = [
            'Name',
            'Telegram ID',
            'Email',
            'Locale',
            'Last Active',
            'Member Since',
        ];

        for (const label of labels) {
            expect(wrapper.text()).toContain(label);
        }
    });
});

describe('Dashboard with null fields', () => {
    it('shows em dash for null values', async () => {
        const { usePage } = await import('@inertiajs/vue3');

        const nullUser = {
            id: '2',
            name: null,
            email: null,
            telegram_id: null,
            locale: null,
            last_active_at: null,
            created_at: '2025-01-01 00:00:00',
            updated_at: '2025-01-01 00:00:00',
        };

        const nullPageProps = {
            auth: { user: nullUser },
            name: 'OpenHabit',
        };

        vi.mocked(usePage).mockReturnValue({
            props: nullPageProps,
        } as never);

        const wrapper = mountDashboard(nullPageProps);
        const dds = wrapper.findAll('dd');

        // name, telegram_id, email, locale, last_active_at should show —
        expect(dds[0].text()).toBe('—');
        expect(dds[1].text()).toBe('—');
        expect(dds[2].text()).toBe('—');
        expect(dds[3].text()).toBe('—');
        expect(dds[4].text()).toBe('—');
        // created_at is always present
        expect(dds[5].text()).toBe('2025-01-01 00:00:00');
    });
});
