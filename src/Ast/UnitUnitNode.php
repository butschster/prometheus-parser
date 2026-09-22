<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

final class UnitUnitNode
{
    public ?string $unit = null;

    public function __construct(array $children)
    {
        foreach ($children as $child) {
            if ($child->getName() === 'T_METRIC_NAME') {
                $this->unit = $child->getValue();
            }
        }
    }
}
