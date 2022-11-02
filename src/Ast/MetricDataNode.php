<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

use Traversable;

final class MetricDataNode implements \IteratorAggregate
{
    public readonly string $description;
    public readonly string $type;
    public readonly string $name;
    /** @var MetricNode[] */
    public array $metrics;

    /** @param \Phplrt\Lexer\Token\Token[] $children */
    public function __construct(array $children)
    {
        foreach ($children as $child) {
            if ($child instanceof HelpNode) {
                $this->description = $child->description;
                $this->name = $child->metric;
            } elseif ($child instanceof TypeNode) {
                $this->type = $child->type;
            } elseif ($child instanceof MetricNode) {
                $this->metrics[] = $child;
            }
        }
    }

    public function getIterator(): Traversable
    {
        return new \IteratorIterator(new \ArrayIterator($this->metrics));
    }
}
