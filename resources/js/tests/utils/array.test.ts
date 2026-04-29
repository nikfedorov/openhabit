import { describe, expect, it } from 'vitest';
import { toggleSortedItem } from '@/utils/array';

describe('toggleSortedItem', () => {
    it('adds item in sorted position', () => {
        expect(toggleSortedItem([1, 3], 2)).toEqual([1, 2, 3]);
    });

    it('adds item to empty array', () => {
        expect(toggleSortedItem([], 5)).toEqual([5]);
    });

    it('removes item if already present', () => {
        expect(toggleSortedItem([1, 2, 3], 2)).toEqual([1, 3]);
    });

    it('does not mutate the original array', () => {
        const original = [1, 2, 3];
        toggleSortedItem(original, 2);
        expect(original).toEqual([1, 2, 3]);
    });
});
