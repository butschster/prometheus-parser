<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

use Phplrt\Lexer\Token\Token;

final class UnitUnitNode
{
    public ?string $unit = null;

    public function __construct(Token $value)
    {
        $this->unit = \trim($value->getValue());
    }
}
