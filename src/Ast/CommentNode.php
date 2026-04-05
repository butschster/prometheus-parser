<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;


final class CommentNode
{
    public ?string $comment = null;

    public function __construct(array $children)
    {
        foreach ($children as $child) {
            if ($child->getName() === 'T_COMMENT') {
                $this->comment = \stripslashes(\strtr(\substr(\trim($child->getValue()), 2), ['\n' => "\n"]));
            }
        }
    }
}
