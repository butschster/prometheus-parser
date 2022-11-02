<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

final class TypeNode
{
    public readonly string $metric;
    public readonly string $type;

    /** @param \Phplrt\Lexer\Token\Token[] $children */
    public function __construct(array $children)
    {
        foreach ($children as $child) {
            if ($child->getName() === 'T_METRIC_NAME') {
                $this->metric = \trim($child->getValue());
            } elseif ($child->getName() === 'T_METRIC_TYPE') {
                $this->type = \trim($child->getValue());
            }
        }
    }
}
