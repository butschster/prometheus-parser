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
            'T_INT' => (int)$token->getValue(),
            'T_FLOAT' => (float)$token->getValue(),
            'T_INF' => \str_starts_with($token->getValue(), '-') ? -\INF : \INF,
            'T_NAN' => \NAN,
        };
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
