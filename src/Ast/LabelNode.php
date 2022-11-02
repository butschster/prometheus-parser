<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

final class LabelNode
{
    public readonly string $name;
    public readonly string $value;

    /** @param \Phplrt\Lexer\Token\Token[] $children */
    public function __construct(array $children)
    {
        foreach ($children as $child) {
            if ($child->getName() === 'T_METRIC_NAME') {
                $this->name = \trim($child->getValue());
            } elseif ($child->getName() === 'T_QUOTED_STRING') {
                $this->value = \stripslashes(\substr($child->getValue(), 1, -1));
            }
        }
    }
}
