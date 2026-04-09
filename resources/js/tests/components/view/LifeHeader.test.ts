import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import LifeHeader from '@/components/view/LifeHeader.vue';
import {
    defaultLifeStats,
    defaultViewTranslations,
} from '@/tests/helpers/view';

describe('LifeHeader', () => {
    it('renders life stats', () => {
        const wrapper = mount(LifeHeader, {
            props: {
                lifeStats: defaultLifeStats,
                translations: defaultViewTranslations,
            },
        });
        expect(wrapper.text()).toContain('Memento Mori');
        expect(wrapper.text()).toContain('25');
        expect(wrapper.text()).toContain('55');
        expect(wrapper.text()).toContain('1,300');
        expect(wrapper.text()).toContain('years old');
        expect(wrapper.text()).toContain('years left');
        expect(wrapper.text()).toContain('weeks lived');
    });
});
