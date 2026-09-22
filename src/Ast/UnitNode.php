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
        // The family name may lex as any token that MetricName() admits, so it
        // is taken by position rather than by token name.
        $nameSet = false;

        foreach ($children as $child) {
            if ($child instanceof UnitUnitNode) {
                $unit = $child->unit;
            } elseif (!$nameSet) {
                $this->metric = match ($child->getName()) {
                    'T_QUOTED_STRING' => \stripslashes(\strtr(\substr($child->getValue(), 1, -1), ['\n' => "\n"])),
                    default => \trim($child->getValue()),
                };
                $nameSet = true;
            }
        }

        $this->unit = $unit;
    }
}
