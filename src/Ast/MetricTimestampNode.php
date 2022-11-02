<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

use Phplrt\Lexer\Token\Token;

final class MetricTimestampNode
{
    public ?int $timestamp = null;

    public function __construct(Token $value)
    {
        $this->timestamp = (int) $value->getValue();
    }
}
