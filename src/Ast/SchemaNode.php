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

    public readonly ?bool $eof;

    /**
     * @param (MetricDataNode|EofNode)[] $children
     */
    public function __construct(array $children)
    {
        // EOF is present in OpenMetrics but not in Prometheus Text Format
        $eof = null;

        foreach ($children as $child) {
            if ($child instanceof EofNode) {
                $eof = true;
            } else {
                $this->metrics[$child->name] = $child;
            }
        }

        $this->eof = $eof;
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
