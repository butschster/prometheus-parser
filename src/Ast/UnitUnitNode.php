<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

use Phplrt\Lexer\Token\Token;

final class UnitUnitNode
{
    public ?string $unit = null;

    /** @param Token|Token[] $child */
    public function __construct(Token|array $child)
    {
        if (\is_object($child)) {
            $this->unit = $child->getValue();
        } else {
            $this->unit = implode('', array_map(fn($token): string => $token->getValue(), $child));
        }
    }
}
