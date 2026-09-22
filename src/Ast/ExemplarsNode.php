<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

final class ExemplarsNode
{
    /** @var ExemplarNode[] */
    public readonly array $exemplars;

    public function __construct(array $children)
    {
        $this->exemplars = $children;
    }
}
