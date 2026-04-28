<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

use Butschster\Prometheus\Exceptions\UnexpectedTokenException;

class TypeNodeTest extends TestCase
{
    /**
     * @testWith ["summary"]
     *           ["counter"]
     *           ["gauge"]
     *           ["histogram"]
     *           ["gaugehistogram"]
     *           ["stateset"]
     *           ["info"]
     *           ["unknown"]
     *           ["untyped"]
     */
    function testType(string $type): void
    {
        $node = $this->parser->parse(<<<SCHEMA
# TYPE test_type $type
test_type 0
SCHEMA
        );

        $this->assertSame(
            $type,
            $node->getMetrics()['test_type']->type
        );
    }

    function testInvalidTypeThrowsException(): void
    {
        $this->expectException(UnexpectedTokenException::class);
        $this->parser->parse(<<<'SCHEMA'
# TYPE test_type foobar
test_type 0
SCHEMA
        );
    }
}
