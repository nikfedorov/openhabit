import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import ProgressBar from '@/components/track/ProgressBar.vue';

function mountProgressBar(total: number, completed: number) {
    return mount(ProgressBar, {
        props: {
            totalHabits: total,
            completedCount: completed,
            progressLabel: 'Progress',
            allDoneLabel: '✓ All done!',
        },
    }).vm;
}

describe('ProgressBar', () => {
    it('computes progressIntensity correctly', () => {
        expect(mountProgressBar(10, 0).progressIntensity).toBe(0);
        expect(mountProgressBar(10, 1).progressIntensity).toBe(1);
        expect(mountProgressBar(10, 5).progressIntensity).toBe(3);
        expect(mountProgressBar(10, 8).progressIntensity).toBe(4);
        expect(mountProgressBar(0, 0).progressIntensity).toBe(0);
    });

    it('returns correct segmentColor', () => {
        expect(mountProgressBar(3, 0).segmentColor).toBe('bg-green-500');
    });
});
