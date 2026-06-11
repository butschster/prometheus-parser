<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

final class UnitNode
{
    public readonly string $metric;
    public readonly ?string $unit;

    public function __construct(array $children)
    {
        // UNIT with empty value string must be treated as if it were not present
        $unit = null;

        foreach ($children as $child) {
            if ($child instanceof UnitUnitNode) {
                $unit = $child->unit;
            } elseif ($child->getName() === 'T_METRIC_NAME') {
                $this->metric = \trim($child->getValue());
            } elseif ($child->getName() === 'T_QUOTED_STRING') {
                $this->metric = \stripslashes(\strtr(\substr($child->getValue(), 1, -1), ['\n' => "\n"]));
            }
        }

        $this->unit = $unit;
    }
}
