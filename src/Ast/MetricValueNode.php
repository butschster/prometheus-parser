<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

use Phplrt\Lexer\Token\Token;

final class MetricValueNode
{
    public readonly float|int $value;
    public readonly string $originalValue;

    public function __construct(Token $value)
    {
        $this->originalValue = $value->getValue();
        $this->value = match ($value->getName()) {
            'T_INT' => (int)$value->getValue(),
            'T_FLOAT' => (float)$value->getValue(),
            'T_INF' => INF,
            'T_NAN' => NAN,
        };
    }
}
