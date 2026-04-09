import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import ActivityCell from '@/components/view/ActivityCell.vue';
import LifeGrid from '@/components/view/LifeGrid.vue';
import { defaultViewTranslations } from '@/tests/helpers/view';
import type { YearActivityData } from '@/types/view';

describe('LifeGrid', () => {
    it('renders 80 year cells with quote', () => {
        const wrapper = mount(LifeGrid, {
            props: {
                birthdate: '2001-03-15',
                currentAge: 25,
                yearlyActivity: null,
                translations: defaultViewTranslations,
            },
        });
        const cells = wrapper.findAllComponents(ActivityCell);
        expect(cells).toHaveLength(80);
        expect(wrapper.text()).toContain('Seneca');
    });

    it('does not render when birthdate is null', () => {
        const wrapper = mount(LifeGrid, {
            props: {
                birthdate: null,
                currentAge: null,
                yearlyActivity: null,
                translations: defaultViewTranslations,
            },
        });
        expect(wrapper.findAllComponents(ActivityCell)).toHaveLength(0);
    });

    it('emits selectYear on cell click', async () => {
        const wrapper = mount(LifeGrid, {
            props: {
                birthdate: '2001-03-15',
                currentAge: 25,
                yearlyActivity: null,
                translations: defaultViewTranslations,
            },
        });
        const firstCell = wrapper.findComponent(ActivityCell);
        await firstCell.trigger('click');
        expect(wrapper.emitted('selectYear')).toBeTruthy();
        expect(wrapper.emitted('selectYear')![0]).toEqual([0]);
    });

    it('uses yearly activity data for intensity', () => {
        const activity: YearActivityData[] = Array.from(
            { length: 80 },
            (_, i) => ({
                year: i,
                intensity: i === 25 ? 4 : 0,
                completed: i === 25 ? 100 : 0,
                total: i === 25 ? 100 : 0,
            }),
        );
        const wrapper = mount(LifeGrid, {
            props: {
                birthdate: '2001-03-15',
                currentAge: 25,
                yearlyActivity: activity,
                translations: defaultViewTranslations,
            },
        });
        const cells = wrapper.findAllComponents(ActivityCell);
        // Current year cell should have ring
        expect(cells[25].html()).toContain('ring-green-500');
    });

    it('shows year labels on decade markers', () => {
        const wrapper = mount(LifeGrid, {
            props: {
                birthdate: '2001-03-15',
                currentAge: 25,
                yearlyActivity: null,
                translations: defaultViewTranslations,
            },
        });
        // Year 0, 10, 20, 30, etc. should show labels
        expect(wrapper.text()).toContain('0');
        expect(wrapper.text()).toContain('10');
        expect(wrapper.text()).toContain('20');
    });
});
