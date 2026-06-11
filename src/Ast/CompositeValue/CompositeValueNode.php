<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast\CompositeValue;

use Butschster\Prometheus\Ast\NumberNode;

abstract class CompositeValueNode
{
    private readonly array $children;

    public function __construct(array $children)
    {
        $this->children = $children;
    }

    public function __toString(): string
    {
        return implode('', array_map(fn($child): string => $child->getValue(), $this->children));
    }

    protected function scalar(string $property, array &$children): void
    {
        array_shift($children); // T_COLON
        $this->{$property} = array_shift($children)->value;
    }

    protected function spans(string $property, array &$children): void
    {
        array_shift($children); // T_COLON
        array_shift($children); // T_LBRACKET
        $items = [];

        while (($child = array_shift($children)) instanceof NumberNode) {
            array_shift($children); // T_COLON
            $items[$child->value] = array_shift($children)->value;
            array_shift($children); // T_COMMA or T_RBRACKET
        }

        $this->{$property} = $items;
    }

    protected function buckets(string $property, array &$children): void
    {
        array_shift($children); // T_COLON
        array_shift($children); // T_LBRACKET
        $items = [];

        while (($child = array_shift($children)) instanceof NumberNode) {
            $items[] = $child->value;
            array_shift($children); // T_COMMA or T_RBRACKET
        }

        $this->{$property} = $items;
    }

    protected function quantiles(string $property, array &$children): void
    {
        array_shift($children); // T_COLON
        array_shift($children); // T_LBRACKET
        $items = [];

        while (($child = array_shift($children)) instanceof NumberNode) {
            array_shift($children); // T_COLON
            $items[(string)$child] = array_shift($children)->value;
            array_shift($children); // T_COMMA or T_RBRACKET
        }

        $this->{$property} = $items;
    }
}
