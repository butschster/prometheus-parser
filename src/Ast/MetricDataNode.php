<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

use Traversable;

final class MetricDataNode implements \IteratorAggregate
{
    // not readonly: a family may be declared by more than one block, and the
    // blocks are merged into the first of them, see merge()
    public ?string $description;
    public string $type;
    public ?string $unit;
    public readonly string $name;
    /** @var MetricNode[] */
    public array $metrics = [];

    public function __construct(array $children)
    {
        // from HELP if available
        $description = null;
        // from TYPE if available
        $type = 'unknown';
        // from UNIT if available
        $unit = null;
        // from either HELP, TYPE, or UNIT
        $name = '';
        // from a comment preceding the first sample of the block
        $comment = null;

        foreach ($children as $child) {
            if ($child instanceof HelpNode) {
                $description = $child->description;
                $name = $child->metric;
            } elseif ($child instanceof TypeNode) {
                $type = $child->type;
                $name = $child->metric;
            } elseif ($child instanceof UnitNode) {
                $unit = $child->unit;
                $name = $child->metric;
            } elseif ($child instanceof CommentNode) {
                $comment = $child->comment;
            } elseif ($child instanceof MetricNode) {
                // a comment ahead of the block belongs to its first sample
                $child->comment ??= $comment;
                $comment = null;
                $this->metrics[] = $child;
            }
        }

        // For bare metric blocks (no TYPE/HELP/UNIT), derive name from first metric.
        if ($name === '' && !empty($this->metrics)) {
            $name = $this->metrics[0]->name;
        }

        $this->description = $description;
        $this->type = $type;
        $this->unit = $unit;
        $this->name = $name;
    }

    /**
     * Merge a later block declaring the same family: its samples are appended
     * and its headers fill in whatever this block did not declare.
     */
    public function merge(self $other): void
    {
        foreach ($other->metrics as $metric) {
            $this->metrics[] = $metric;
        }

        $this->description ??= $other->description;
        $this->unit ??= $other->unit;

        if ($this->type === 'unknown') {
            $this->type = $other->type;
        }
    }

    public function getIterator(): Traversable
    {
        return new \IteratorIterator(new \ArrayIterator($this->metrics));
    }

    // -------------------------------------------------------------------------
    //  Sub-metric accessors (OpenMetrics grouping by well-known suffixes)
    // -------------------------------------------------------------------------

    /**
     * Returns the _total sample for a counter family (name: "{family}_total").
     */
    public function getTotal(): ?MetricNode
    {
        return $this->findFirstBySuffix('_total');
    }

    /**
     * Returns the _created sample, present on counter / summary / histogram families.
     */
    public function getCreated(): ?MetricNode
    {
        return $this->findFirstBySuffix('_created');
    }

    /**
     * Returns the _sum sample for summary / histogram families.
     */
    public function getSum(): ?MetricNode
    {
        return $this->findFirstBySuffix('_sum');
    }

    /**
     * Returns the _count sample for summary / histogram families.
     */
    public function getCount(): ?MetricNode
    {
        return $this->findFirstBySuffix('_count');
    }

    /**
     * Returns all _bucket samples for histogram / gaugehistogram families.
     *
     * @return MetricNode[]
     */
    public function getBuckets(): array
    {
        return $this->findAllBySuffix('_bucket');
    }

    /**
     * Returns the _gsum sample for gaugehistogram families.
     */
    public function getGSum(): ?MetricNode
    {
        return $this->findFirstBySuffix('_gsum');
    }

    /**
     * Returns the _gcount sample for gaugehistogram families.
     */
    public function getGCount(): ?MetricNode
    {
        return $this->findFirstBySuffix('_gcount');
    }

    private function findFirstBySuffix(string $suffix): ?MetricNode
    {
        $target = $this->name . $suffix;
        foreach ($this->metrics as $metric) {
            if ($metric->name === $target) {
                return $metric;
            }
        }
        return null;
    }

    /** @return MetricNode[] */
    private function findAllBySuffix(string $suffix): array
    {
        $target = $this->name . $suffix;
        return \array_values(\array_filter($this->metrics, fn(MetricNode $m) => $m->name === $target));
    }
}
