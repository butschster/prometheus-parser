<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class MetricStartTimestampNodeTest extends TestCase
{
    function testNoStartTimestamp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_no_start_timestamp counter
test_no_start_timestamp 0
SCHEMA
        );

        $this->assertNull(
            $node->getMetrics()['test_no_start_timestamp']->metrics[0]->startTimestamp
        );
    }

    function testIntStartTimestamp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_start_timestamp counter
test_start_timestamp 0 st@9876543210
test_start_timestamp 1 st@-123456789
SCHEMA
        );

        $this->assertSame(
            9876543210,
            $node->getMetrics()['test_start_timestamp']->metrics[0]->startTimestamp
        );

        $this->assertSame(
            -123456789,
            $node->getMetrics()['test_start_timestamp']->metrics[1]->startTimestamp
        );
    }

    function testFloatStartTimestamp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_start_timestamp counter
test_start_timestamp 0 st@987654321.123
test_start_timestamp 1 st@-12345678.987
SCHEMA
        );

        $this->assertSame(
            987654321.123,
            $node->getMetrics()['test_start_timestamp']->metrics[0]->startTimestamp
        );

        $this->assertSame(
            -12345678.987,
            $node->getMetrics()['test_start_timestamp']->metrics[1]->startTimestamp
        );
    }

    function testTimestampAndStartTimestamp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_timestamp counter
test_timestamp 0 987654321.123 st@-12345678.987
SCHEMA
        );

        $this->assertSame(
            -12345678.987,
            $node->getMetrics()['test_timestamp']->metrics[0]->startTimestamp
        );
    }
}
