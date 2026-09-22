<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

/**
 * Unescaping of the sequences the text format defines: \\, \n and \".
 */
final class EscapeSequence
{
    /**
     * A single left-to-right pass, so that an escaped backslash is honoured
     * before the character that follows it. Any other backslash is kept as is,
     * the way both formats read it literally.
     */
    public static function unescape(string $value): string
    {
        return \preg_replace_callback(
            '/\\\\(.)/s',
            static fn(array $matches): string => match ($matches[1]) {
                'n' => "\n",
                '"' => '"',
                '\\' => '\\',
                default => $matches[0],
            },
            $value
        ) ?? $value;
    }

    /**
     * Unescape a quoted string, dropping the surrounding quotes.
     */
    public static function unquote(string $value): string
    {
        return self::unescape(\substr($value, 1, -1));
    }
}
