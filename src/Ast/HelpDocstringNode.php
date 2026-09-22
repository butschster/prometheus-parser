<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

final class HelpDocstringNode
{
    public ?string $description = null;

    public function __construct(array $children)
    {
        foreach ($children as $child) {
            if ($child instanceof MetricValueNode) {
                $this->description .= (string)$child;
            } elseif ($child->getName() === 'T_TEXT') {
                $this->description .= EscapeSequence::unescape(\trim($child->getValue()));
            } else {
                $this->description .= $child->getValue();
            }
        }
    }
}
