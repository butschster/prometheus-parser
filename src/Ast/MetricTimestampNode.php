<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

use Phplrt\Lexer\Token\Token;

final class MetricTimestampNode
{
    public int|float|null $timestamp = null;

    public function __construct(Token $value)
    {
        $this->timestamp = match ($value->getName()) {
            'T_INT' => NumberNode::toInteger($value->getValue()),
            'T_FLOAT' => (float)$value->getValue(),
        };
    }
}
