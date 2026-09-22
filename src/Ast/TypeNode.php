<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

final class TypeNode
{
    public readonly string $metric;
    public readonly string $type;

    /** @param \Phplrt\Lexer\Token\Token[] $children */
    public function __construct(array $children)
    {
        // The family name may lex as any token that MetricName() admits, which
        // includes T_METRIC_TYPE, so name and type are told apart by position.
        $nameSet = false;
        foreach ($children as $child) {
            if (!$nameSet) {
                $this->metric = match ($child->getName()) {
                    'T_QUOTED_STRING' => \stripslashes(\strtr(\substr($child->getValue(), 1, -1), ['\n' => "\n"])),
                    default => \trim($child->getValue()),
                };
                $nameSet = true;
            } else {
                $this->type = \trim($child->getValue());
            }
        }
    }
}
