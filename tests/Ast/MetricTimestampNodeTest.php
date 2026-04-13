<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class MetricTimestampNodeTest extends TestCase
{
    function testNoTimestamp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_no_timestamp counter
test_no_timestamp 0
SCHEMA
        );

        $this->assertNull(
            $node->getMetrics()['test_no_timestamp']->metrics[0]->timestamp
        );
    }

    function testIntTimestamp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_timestamp counter
test_timestamp 0 9876543210
test_timestamp 1 -123456789
SCHEMA
        );

        $this->assertSame(
            9876543210,
            $node->getMetrics()['test_timestamp']->metrics[0]->timestamp
        );

        $this->assertSame(
            -123456789,
            $node->getMetrics()['test_timestamp']->metrics[1]->timestamp
        );
    }

    function testFloatTimestamp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_timestamp counter
test_timestamp 0 987654321.123
test_timestamp 1 -12345678.987
SCHEMA
        );

        $this->assertSame(
            987654321.123,
            $node->getMetrics()['test_timestamp']->metrics[0]->timestamp
        );

        $this->assertSame(
            -12345678.987,
            $node->getMetrics()['test_timestamp']->metrics[1]->timestamp
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
            987654321.123,
            $node->getMetrics()['test_timestamp']->metrics[0]->timestamp
        );
    }
}
