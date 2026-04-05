<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Parsers;

abstract class TestCase extends \Butschster\Prometheus\Tests\TestCase
{
    public function assertAst(string $schema, string $ast)
    {
        $ast = \array_map(function (string $line) {
            if (empty($line)) {
                return $line;
            }
            return $line;
        }, \explode("\n", $ast));

        $ast = \implode("\n", $ast);

        $this->assertEquals(
            $ast,
            (string)self::$compiler->parse($schema)
        );
    }
}
