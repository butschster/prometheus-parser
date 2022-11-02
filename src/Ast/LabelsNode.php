<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

final class LabelsNode
{
    public readonly array $labels;

    public function __construct(array $children)
    {
        $this->labels = $children;
    }
}
