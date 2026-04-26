<?php

declare(strict_types=1);

namespace App\Logging;

use App\Jobs\SendTelegramErrorAlertJob;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

/**
 * Monolog handler that ships log records to a Telegram channel asynchronously.
 *
 * On each qualifying log record a SendTelegramErrorAlertJob is dispatched to
 * the queue, keeping the exception handler fast and Nutgram-backed.
 */
final class TelegramMonologHandler extends AbstractProcessingHandler
{
    /** Telegram's hard limit is 4096; we use 4000 to leave room for formatting overhead. */
    private const int MAX_LENGTH = 4000;

    /**
     * @param  Level  $level  Minimum record level to handle (injected by Laravel's log manager).
     * @param  string  $chatId  Target chat / channel ID.
     * @param  bool  $bubble  Whether records handled here bubble up.
     *
     * Note: $level must be the first parameter so it aligns with Laravel's monolog
     * driver convention of prepending the resolved level before handler_with args.
     */
    public function __construct(
        Level $level = Level::Error,
        private readonly string $chatId = '',
        bool $bubble = true,
    ) {
        parent::__construct($level, $bubble);
    }

    /**
     * Dispatch the error alert job for a qualifying log record.
     */
    protected function write(LogRecord $record): void
    {
        if ($this->chatId === '') {
            return;
        }

        $text = $this->formatRecord($record);

        dispatch(new SendTelegramErrorAlertJob(
            chatId: $this->chatId,
            text: $text,
        ));
    }

    /**
     * Build the HTML-formatted Telegram message text from a log record.
     */
    private function formatRecord(LogRecord $record): string
    {
        $emoji = match ($record->level) {
            Level::Emergency, Level::Alert, Level::Critical => '🔴',
            Level::Error => '❌',
            Level::Warning => '⚠️',
            default => 'ℹ️',
        };

        $env = config()->string('app.env', 'unknown');
        $lines = [
            sprintf('%s <b>[%s]</b> %s', $emoji, $record->level->name, $env),
            '',
            htmlspecialchars($record->message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
        ];

        if ($record->context !== []) {
            $json = json_encode($record->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $lines[] = '';
            $lines[] = sprintf("<pre>Context:\n%s</pre>", htmlspecialchars((string) $json, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
        }

        $text = implode("\n", $lines);

        return mb_substr($text, 0, self::MAX_LENGTH);
    }
}
