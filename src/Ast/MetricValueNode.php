<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

final class MetricValueNode
{
    public readonly float|int|CompositeValue\CompositeValueNode $value;
    private readonly NumberNode|CompositeValue\CompositeValueNode $node;

    public function __construct(NumberNode|CompositeValue\CompositeValueNode $node)
    {
        $this->node = $node;

        if ($node instanceof NumberNode) {
            $this->value = $node->value;
        } else {
            $this->value = $node;
        }
    }

    public function __toString(): string
    {
        return (string)$this->node;
    }
}
