<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

use Traversable;

final class MetricDataNode implements \IteratorAggregate
{
    public readonly ?string $description;
    public readonly string $type;
    public readonly ?string $unit;
    public readonly string $name;
    /** @var MetricNode[] */
    public array $metrics;

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
            } elseif ($child instanceof MetricNode) {
                $this->metrics[] = $child;
            }
        }

        $this->description = $description;
        $this->type = $type;
        $this->unit = $unit;
        $this->name = $name;
    }

    public function getIterator(): Traversable
    {
        return new \IteratorIterator(new \ArrayIterator($this->metrics));
    }
}
