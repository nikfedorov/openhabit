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
        $env = config()->string('app.env', 'unknown');
        $label = ucfirst(mb_strtolower($record->level->name));

        $lines = [
            sprintf('<b>%s</b> · %s', $label, htmlspecialchars($env, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')),
            '',
            htmlspecialchars($record->message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
        ];

        if ($record->context !== []) {
            $maxKeyLength = max(array_map(mb_strlen(...), array_keys($record->context)));
            $contextLines = [];

            foreach ($record->context as $key => $value) {
                $displayValue = match (true) {
                    is_string($value) => $value,
                    is_int($value), is_float($value) => (string) $value,
                    is_bool($value) => $value ? 'true' : 'false',
                    $value === null => 'null',
                    default => (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                };

                $contextLines[] = mb_str_pad((string) $key, $maxKeyLength).'  '.$displayValue;
            }

            $contextText = htmlspecialchars(implode("\n", $contextLines), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $lines[] = '';
            $lines[] = sprintf('<code>%s</code>', $contextText);
        }

        $text = implode("\n", $lines);

        return mb_substr($text, 0, self::MAX_LENGTH);
    }
}
