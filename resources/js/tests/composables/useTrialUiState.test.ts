import { beforeEach, describe, expect, it, vi } from 'vitest';
import { useTrialUiState } from '@/composables/useTrialUiState';
import { defaultTrial } from '@/tests/helpers/settings';

const { mockApiFetch } = vi.hoisted(() => ({
    mockApiFetch: vi.fn(),
}));

vi.mock('@/utils/api', () => ({
    apiFetch: mockApiFetch,
}));

beforeEach(() => {
    mockApiFetch.mockReset();
    // Reset shared singleton state between tests.
    const { setTrialData, closePremiumModal } = useTrialUiState();
    setTrialData(null);
    closePremiumModal();
});

describe('useTrialUiState', () => {
    it('stores trial data when it is provided', () => {
        const { trialData, setTrialData } = useTrialUiState();

        setTrialData(defaultTrial);

        expect(trialData.value).toEqual(defaultTrial);
    });

    it('ignores undefined trial data updates', () => {
        const { trialData, setTrialData } = useTrialUiState();

        setTrialData(defaultTrial);
        setTrialData(undefined);

        expect(trialData.value).toEqual(defaultTrial);
    });

    it('opens and closes the premium modal', () => {
        const { showPremiumModal, openPremiumModal, closePremiumModal } =
            useTrialUiState();

        openPremiumModal();
        expect(showPremiumModal.value).toBe(true);

        closePremiumModal();
        expect(showPremiumModal.value).toBe(false);
    });

    it('does nothing when dismissing without a visible banner', async () => {
        const { dismissTrialBanner } = useTrialUiState();

        await dismissTrialBanner();

        expect(mockApiFetch).not.toHaveBeenCalled();
    });

    it('dismisses the trial banner optimistically', async () => {
        mockApiFetch.mockResolvedValueOnce({ ok: true });

        const { trialData, setTrialData, dismissTrialBanner } =
            useTrialUiState();

        setTrialData({ ...defaultTrial, shouldShowBanner: true });

        await dismissTrialBanner();

        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings/trial-banner/dismiss',
            { method: 'POST' },
        );
        expect(trialData.value?.shouldShowBanner).toBe(false);
    });

    it('restores the banner when dismissal fails', async () => {
        mockApiFetch.mockRejectedValueOnce(new Error('Network error'));

        const { trialData, setTrialData, dismissTrialBanner } =
            useTrialUiState();

        setTrialData({ ...defaultTrial, shouldShowBanner: true });

        await expect(dismissTrialBanner()).rejects.toThrow('Network error');
        expect(trialData.value?.shouldShowBanner).toBe(true);
    });
});
