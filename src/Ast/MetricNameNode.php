<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Ast;

use Phplrt\Lexer\Token\Token;

final class MetricNameNode
{
    public readonly string $value;

    /** @param Token|Token[] $child */
    public function __construct(Token|array $child)
    {
        if (\is_object($child)) {
            $this->value = $child->getValue();
        } else {
            $this->value = implode('', array_map(fn($token): string => $token->getValue(), $child));
        }
    }

    public function getName(): string
    {
        return 'T_METRIC_NAME';
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
