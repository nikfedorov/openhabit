import { ref } from 'vue';
import type { TrialData } from '@/types/api';
import { apiFetch } from '@/utils/api';

export function useTrialUiState() {
    const trialData = ref<TrialData | null>(null);
    const showPremiumModal = ref(false);

    function setTrialData(nextTrialData?: TrialData | null) {
        if (nextTrialData === undefined) {
            return;
        }

        trialData.value = nextTrialData;
    }

    function openPremiumModal() {
        showPremiumModal.value = true;
    }

    function closePremiumModal() {
        showPremiumModal.value = false;
    }

    async function dismissTrialBanner() {
        if (trialData.value === null || !trialData.value.shouldShowBanner) {
            return;
        }

        const currentTrialData = trialData.value;

        trialData.value = { ...currentTrialData, shouldShowBanner: false };

        try {
            await apiFetch('/api/settings/trial-banner/dismiss', {
                method: 'POST',
            });
        } catch (error) {
            trialData.value = currentTrialData;

            throw error;
        }
    }

    return {
        trialData,
        showPremiumModal,
        setTrialData,
        openPremiumModal,
        closePremiumModal,
        dismissTrialBanner,
    };
}
