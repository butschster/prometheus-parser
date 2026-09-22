<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class UnitNodeTest extends TestCase
{
    function testUnit(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_unit summary
# UNIT test_unit bytes
test_unit 0
# TYPE test_wild_unit summary
# UNIT test_wild_unit m:s_2
test_wild_unit 0
SCHEMA
        );

        $this->assertSame(
            'bytes',
            $node->getMetrics()['test_unit']->unit
        );

        $this->assertSame(
            'm:s_2',
            $node->getMetrics()['test_wild_unit']->unit
        );
    }

    function testUnitContainingOtherToken(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_nanoseconds Test that the unit isn't misinterpreted as T_NAN + oseconds
# TYPE test_nanoseconds histogram
# UNIT test_nanoseconds nanoseconds
test_nanoseconds_bucket{le="+Inf"} 537
SCHEMA
        );

        $this->assertSame(
            'nanoseconds',
            $node->getMetrics()['test_nanoseconds']->unit
        );
    }

    function testEmptyUnit(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_empty_unit summary
# UNIT test_empty_unit 
test_empty_unit 0
SCHEMA
        );

        $this->assertNull(
            $node->getMetrics()['test_empty_unit']->unit
        );
    }

    function testNoUnit(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_no_unit summary
test_no_unit 0
SCHEMA
        );

        $this->assertNull(
            $node->getMetrics()['test_no_unit']->unit
        );
    }
}
