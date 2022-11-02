<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

use Phplrt\Lexer\Token\Token;

final class MetricValueNode
{
    public readonly float|int $value;

    public function __construct(Token $value)
    {
        $this->value = \ctype_digit($value->getValue())
            ? (int)$value->getValue()
            : (float)$value->getValue();
    }
}
