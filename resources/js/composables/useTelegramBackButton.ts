import { onMounted, onUnmounted } from 'vue';

/**
 * Returns true when the app is running inside a Telegram WebApp context.
 * Checked once per call — safe to use as a plain const in `<script setup>`.
 */
export function isTelegram(): boolean {
    return !!window.Telegram?.WebApp?.initData;
}

/**
 * Shows the Telegram native BackButton while this component is mounted,
 * and hides it when the component unmounts.
 *
 * No-op when not running inside Telegram or on an older WebApp version.
 *
 * @param onBack - Called when the user taps the native back button.
 */
export function useTelegramBackButton(onBack: () => void): void {
    const tg = window.Telegram?.WebApp;
    const isAvailable = tg?.isVersionAtLeast?.('6.1') && !!tg?.BackButton;

    onMounted(() => {
        if (!isAvailable) return;
        tg!.BackButton.show();
        tg!.BackButton.onClick(onBack);
    });

    onUnmounted(() => {
        if (!isAvailable) return;
        tg!.BackButton.offClick(onBack);
        tg!.BackButton.hide();
    });
}
