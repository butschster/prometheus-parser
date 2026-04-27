<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast\CompositeValue;

final class SummaryValueNode extends CompositeValueNode
{
    public readonly float|int $count;
    public readonly float|int $sum;
    /** @var array<string, float|int> */
    public readonly array $quantile;

    public function __construct(array $children)
    {
        parent::__construct($children);

        while (null !== $child = array_shift($children)) {
            switch ($child->getName()) {
                case 'T_LBRACE':
                case 'T_RBRACE':
                case 'T_COMMA':
                    break;
                case 'T_COUNT':
                case 'T_SUM':
                    $this->scalar($child->getValue(), $children);
                    break;
                case 'T_QUANTILE':
                    $this->quantiles($child->getValue(), $children);
                    break;
            }
        }
    }
}
