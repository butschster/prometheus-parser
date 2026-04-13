<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

/**
 * Represents an OpenMetrics exemplar attached to a metric sample.
 *
 * Example: # {trace_id="abc"} 1.0 1395066363.000
 */
final class ExemplarNode
{
    /** @var LabelNode[] */
    public readonly array $labels;
    public readonly float|int $value;
    public int|float|null $timestamp = null;

    public function __construct(array $children)
    {
        $labels = [];
        $value = null;

        foreach ($children as $child) {
            if ($child instanceof LabelsNode) {
                $labels = $child->labels;
            } elseif ($child instanceof MetricValueNode) {
                $value = $child->value;
            } elseif ($child instanceof MetricTimestampNode) {
                $this->timestamp = $child->timestamp;
            }
        }

        $this->labels = $labels;
        $this->value = $value;
    }
}
