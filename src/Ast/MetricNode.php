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
    public ?ExemplarNode $exemplar = null;

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
            } elseif ($child instanceof ExemplarNode) {
                $this->exemplar = $child;
            } elseif ($child->getName() === 'T_METRIC_NAME') {
                $this->name = \trim($child->getValue());
            } elseif ($child->getName() === 'T_QUOTED_STRING') {
                $this->name = \stripslashes(\strtr(\substr($child->getValue(), 1, -1), ['\n' => "\n"]));
            }
        }
    }
}
