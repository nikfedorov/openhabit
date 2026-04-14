/**
 * Minimal Telegram WebApp global type declarations.
 * https://core.telegram.org/bots/webapps
 */
interface TelegramBackButton {
    isVisible: boolean;
    show(): void;
    hide(): void;
    onClick(callback: () => void): void;
    offClick(callback: () => void): void;
}

interface TelegramWebApp {
    /** Non-empty when opened inside Telegram. */
    initData: string;
    isVersionAtLeast(version: string): boolean;
    BackButton: TelegramBackButton;
}

interface Window {
    Telegram?: {
        WebApp: TelegramWebApp;
    };
}
