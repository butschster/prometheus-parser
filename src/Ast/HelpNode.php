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

        foreach ($children as $child) {
            if ($child instanceof HelpDocstringNode) {
                $description = $child->description;
            } elseif ($child->getName() === 'T_METRIC_NAME') {
                $this->metric = \trim($child->getValue());
            } elseif ($child->getName() === 'T_QUOTED_STRING') {
                $this->metric = \stripslashes(\strtr(\substr($child->getValue(), 1, -1), ['\n' => "\n"]));
            }
        }

        $this->description = $description;
    }
}
