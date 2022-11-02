<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

use Traversable;

/**
 * @psalm-type
 */
final class SchemaNode implements \IteratorAggregate
{
    /** @var non-empty-array<string, MetricDataNode> */
    private array $metrics;

    /**
     * @param MetricDataNode[] $children
     */
    public function __construct(array $children)
    {
        foreach ($children as $child) {
            $this->metrics[$child->name] = $child;
        }
    }

    /**
     * @return array<string, MetricDataNode>
     */
    public function getMetrics(): array
    {
        return $this->metrics;
    }

    public function getIterator(): Traversable
    {
        return new \IteratorIterator(new \ArrayIterator($this->metrics));
    }
}
