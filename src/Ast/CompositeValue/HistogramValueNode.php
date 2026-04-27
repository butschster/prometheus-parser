<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast\CompositeValue;

final class HistogramValueNode extends CompositeValueNode
{
    public float|int $count;
    public float|int $sum;
    public int $schema;
    public float|int $zero_threshold;
    public float|int $zero_count;
    /** @var array<string, int>|null */
    public ?array $negative_spans = null;
    /** @var array<float|int>|null */
    public ?array $negative_buckets = null;
    /** @var array<string, int>|null */
    public ?array $positive_spans = null;
    /** @var array<float|int>|null */
    public ?array $positive_buckets = null;
    /** @var array<string, float|int>|null */
    public ?array $bucket = null;

    public function __construct(array $children)
    {
        parent::__construct($children);

        while (null !== $child = array_shift($children)) {
            switch ($child->getName()) {
                case 'T_LBRACE':
                case 'T_RBRACE':
                case 'T_COMMA':
                    break;
                case 'T_COUNT':
                case 'T_SUM':
                case 'T_SCHEMA':
                case 'T_ZERO_THRESHOLD':
                case 'T_ZERO_COUNT':
                    $this->scalar($child->getValue(), $children);
                    break;
                case 'T_NEGATIVE_SPANS':
                case 'T_POSITIVE_SPANS':
                    $this->spans($child->getValue(), $children);
                    break;
                case 'T_NEGATIVE_BUCKETS':
                case 'T_POSITIVE_BUCKETS':
                    $this->buckets($child->getValue(), $children);
                    break;
                case 'T_BUCKET':
                    $this->quantiles($child->getValue(), $children);
                    break;
            }
        }
    }
}
