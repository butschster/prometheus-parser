<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

final class HelpNode
{
    public readonly string $metric;
    public readonly ?string $description;

    public function __construct(array $children)
    {
        // HELP with empty value string must be treated as if it were not present
        $description = null;
        // The family name may lex as any token that MetricName() admits, so it
        // is taken by position rather than by token name.
        $nameSet = false;

        foreach ($children as $child) {
            if ($child instanceof HelpDocstringNode) {
                $description = $child->description;
            } elseif (!$nameSet) {
                $this->metric = match ($child->getName()) {
                    'T_QUOTED_STRING' => EscapeSequence::unquote($child->getValue()),
                    default => \trim($child->getValue()),
                };
                $nameSet = true;
            }
        }

        $this->description = $description;
    }
}
