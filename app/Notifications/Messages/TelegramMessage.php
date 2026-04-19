<?php

declare(strict_types=1);

namespace App\Notifications\Messages;

use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\WebApp\WebAppInfo;

/**
 * Represents a Telegram text message to be sent via the TelegramChannel.
 */
final class TelegramMessage
{
    /** @var list<string> */
    private array $lines = [];

    private ?string $parseMode = null;

    /** @var list<list<InlineKeyboardButton>> */
    private array $buttonRows = [];

    /**
     * Create a new TelegramMessage instance.
     */
    public static function create(): self
    {
        return new self;
    }

    /**
     * Replace the message text.
     */
    public function text(string $text): self
    {
        $this->lines = [$text];

        return $this;
    }

    /**
     * Append a line to the message text.
     */
    public function line(string $line): self
    {
        $this->lines[] = $line;

        return $this;
    }

    /**
     * Set the parse mode (e.g. 'HTML', 'MarkdownV2').
     */
    public function parseMode(string $mode): self
    {
        $this->parseMode = $mode;

        return $this;
    }

    /**
     * Get the parse mode.
     */
    public function getParseMode(): ?string
    {
        return $this->parseMode;
    }

    /**
     * Add a callback button row.
     */
    public function button(string $text, string $callbackData): self
    {
        $this->buttonRows[] = [
            InlineKeyboardButton::make(text: $text, callback_data: $callbackData),
        ];

        return $this;
    }

    /**
     * Add a web app button row.
     */
    public function webAppButton(string $text, string $url): self
    {
        $this->buttonRows[] = [
            InlineKeyboardButton::make(text: $text, web_app: WebAppInfo::make($url)),
        ];

        return $this;
    }

    /**
     * Get the message text.
     */
    public function getText(): string
    {
        return implode("\n", $this->lines);
    }

    /**
     * Get the inline keyboard markup, or null if no buttons were added.
     */
    public function getReplyMarkup(): ?InlineKeyboardMarkup
    {
        if ($this->buttonRows === []) {
            return null;
        }

        $markup = InlineKeyboardMarkup::make();

        foreach ($this->buttonRows as $row) {
            $markup->addRow(...$row);
        }

        return $markup;
    }
}
