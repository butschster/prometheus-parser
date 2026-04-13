<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

final class MetricNode
{
    public string $name;
    public ?string $comment = null;
    public readonly mixed $value;
    public int|float|null $timestamp = null;
    public int|float|null $startTimestamp = null;
    public array $labels = [];

    public function __construct(array $children)
    {
        foreach ($children as $child) {
            if ($child instanceof LabelsNode) {
                $this->labels = $child->labels;
            } elseif ($child instanceof MetricValueNode) {
                $this->value = $child->value;
            } elseif ($child instanceof MetricTimestampNode) {
                $this->timestamp = $child->timestamp;
            } elseif ($child instanceof MetricStartTimestampNode) {
                $this->startTimestamp = $child->startTimestamp;
            } elseif ($child instanceof CommentNode) {
                $this->comment = $child->comment;
            } elseif ($child->getName() === 'T_METRIC_NAME') {
                $this->name = \trim($child->getValue());
            }
        }
    }
}
