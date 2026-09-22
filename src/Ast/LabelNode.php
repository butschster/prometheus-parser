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
        $nameSet = false;
        foreach ($children as $child) {
            if (!$nameSet) {
                $this->name = match ($child->getName()) {
                    'T_QUOTED_STRING' => EscapeSequence::unquote($child->getValue()),
                    default => \trim($child->getValue()),
                };
                $nameSet = true;
            } else {
                $this->value = EscapeSequence::unquote($child->getValue());
            }
        }
    }
}
