<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

final class HelpNode
{
    public readonly string $metric;
    public readonly string $description;

    /** @param \Phplrt\Lexer\Token\Token[] $children */
    public function __construct(array $children)
    {
        foreach ($children as $child) {
            if ($child->getName() === 'T_METRIC_NAME') {
                $this->metric = \trim($child->getValue());
            } elseif ($child->getName() === 'T_TEXT') {
                $this->description = \trim($child->getValue());
            }
        }
    }
}
