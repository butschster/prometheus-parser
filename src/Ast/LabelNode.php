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
                    'T_METRIC_NAME' => \trim($child->getValue()),
                    default => \stripslashes(\strtr(\substr($child->getValue(), 1, -1), ['\n' => "\n"])),
                };
                $nameSet = true;
            } else {
                $this->value = \stripslashes(\strtr(\substr($child->getValue(), 1, -1), ['\n' => "\n"]));
            }
        }
    }
}
