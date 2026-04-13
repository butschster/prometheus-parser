<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class OpenMetricsTest extends SchemaNodeTest
{
    protected function setNode(): void
    {
        $this->node = $this->parser->parse(<<<'SCHEMA'
# TYPE go_gc_duration_seconds summary
# UNIT go_gc_duration_seconds seconds
# HELP go_gc_duration_seconds A summary of the pause duration of garbage collection cycles.
go_gc_duration_seconds{quantile="0", test="0.25"} 3.332e-05
go_gc_duration_seconds{quantile="0.5"} 4.716e-05
go_gc_duration_seconds{quantile="1"} 0.000218257
go_gc_duration_seconds_sum 0.000298737
# A weird metric from before the epoch:
go_gc_duration_seconds_count 3 218257
# EOF

SCHEMA
        );
    }

    function testSchemaUnit(): void
    {
        $this->assertSame(
            'seconds',
            $this->node->getMetrics()['go_gc_duration_seconds']->unit
        );
    }

    function testSchemaEof() {
        $this->assertTrue(
            $this->node->eof
        );
    }

    function testEmptySchema() {
        $emptyNode = $this->parser->parse(<<<'SCHEMA'
# EOF

SCHEMA
        );

        $this->assertCount(
            0,
            $emptyNode->getMetrics()
        );

        $this->assertTrue(
            $emptyNode->eof
        );
    }
}
