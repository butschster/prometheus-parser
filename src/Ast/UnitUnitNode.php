<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

final class UnitUnitNode
{
    public ?string $unit = null;

    public function __construct(array $children)
    {
        // UnitUnit() is a MetricName(), so the unit may lex as a keyword token
        // such as T_COUNT; dropping it would also disable UnitSuffixValidator.
        $child = \reset($children);

        if ($child !== false) {
            $this->unit = $child->getValue();
        }
    }
}
