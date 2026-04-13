<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

final class MetricStartTimestampNode
{
    public int|float|null $startTimestamp = null;

    /** @param \Phplrt\Lexer\Token\Token[] $children */
    public function __construct(array $children)
    {
        foreach ($children as $child) {
            $this->startTimestamp = match ($child->getName()) {
                'T_INT' => (int)$child->getValue(),
                'T_FLOAT' => (float)$child->getValue(),
            };
        }
    }
}
