<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

use Phplrt\Lexer\Token\Token;

final class NumberNode
{
    public readonly float|int $value;
    private readonly Token $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
        $this->value = match ($token->getName()) {
            'T_INT' => self::toInteger($token->getValue()),
            'T_FLOAT' => (float)$token->getValue(),
            'T_INF' => \str_starts_with($token->getValue(), '-') ? -\INF : \INF,
            'T_NAN' => \NAN,
        };
    }

    /**
     * Read an integer literal, keeping one beyond the integer range as a float
     * rather than clamping it to PHP_INT_MAX, which is what Prometheus reads.
     */
    public static function toInteger(string $value): float|int
    {
        $integer = (int)$value;
        $float = (float)$value;

        // An (int) cast clamps to PHP_INT_MAX, so a clamped reading no longer
        // agrees with the float one.
        return (float)$integer === $float ? $integer : $float;
    }

    /**
     * Return the original value of the token as a string.
     */
    public function getValue(): string
    {
        return $this->token->getValue();
    }

    public function __toString(): string
    {
        return $this->token->getValue();
    }
}
