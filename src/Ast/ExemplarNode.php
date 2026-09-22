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
    public readonly int|float|null $timestamp;

    public function __construct(array $children)
    {
        foreach ($children as $child) {
            if ($child instanceof LabelsNode) {
                $labels = $child->labels;
            } elseif ($child instanceof MetricValueNode) {
                $value = $child->value;
            } elseif ($child instanceof MetricTimestampNode) {
                $timestamp = $child->timestamp;
            }
        }

        // a LabelSet can be empty
        $this->labels = $labels ?? [];
        // (wouldn't parse without a value but statistical analysis can't know this)
        $this->value = $value ?? NAN;
        // timestamp is optional in OpenMetrics 1.0
        $this->timestamp = $timestamp ?? null;
    }
}
