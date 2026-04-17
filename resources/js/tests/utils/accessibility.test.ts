import { afterEach, describe, expect, it, vi } from 'vitest';
import { prefersReducedMotion } from '@/utils/accessibility';

describe('prefersReducedMotion', () => {
    afterEach(() => {
        vi.unstubAllGlobals();
    });

    it('returns false when matchMedia is unavailable', () => {
        vi.stubGlobal('window', {
            matchMedia: undefined,
        });

        expect(prefersReducedMotion()).toBe(false);
    });

    it('returns the reduced motion media query result when available', () => {
        vi.stubGlobal('window', {
            matchMedia: vi.fn().mockReturnValue({ matches: true }),
        });

        expect(prefersReducedMotion()).toBe(true);
    });
});
