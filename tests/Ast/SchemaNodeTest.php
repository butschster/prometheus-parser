<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

use Butschster\Prometheus\Ast\SchemaNode;

class SchemaNodeTest extends TestCase
{
    private ?SchemaNode $node;

    protected function setUp(): void
    {
        parent::setUp();

        $this->node = $this->parser->parse(<<<SCHEMA
# HELP go_gc_duration_seconds A summary of the pause duration of garbage collection cycles.
# TYPE go_gc_duration_seconds summary
go_gc_duration_seconds{quantile="0", test="0.25"} 3.332e-05
go_gc_duration_seconds{quantile="0.5"} 4.716e-05
go_gc_duration_seconds{quantile="1"} 0.000218257
go_gc_duration_seconds_sum 0.000298737
go_gc_duration_seconds_count 3
SCHEMA
        );
    }

    function testSchemaHelp(): void
    {
        $this->assertSame(
            'A summary of the pause duration of garbage collection cycles.',
            $this->node->getMetrics()['go_gc_duration_seconds']->description
        );
    }

    function testSchemaType(): void
    {
        $this->assertSame(
            'summary',
            $this->node->getMetrics()['go_gc_duration_seconds']->type
        );
    }

    function testSchemaValue(): void
    {
        $this->assertSame(
            4.716E-5,
            $this->node->getMetrics()['go_gc_duration_seconds']->metrics[1]->value
        );
        $this->assertSame(
            'go_gc_duration_seconds',
            $this->node->getMetrics()['go_gc_duration_seconds']->metrics[1]->name
        );

        $this->assertSame(
            0.000298737,
            $this->node->getMetrics()['go_gc_duration_seconds']->metrics[3]->value
        );

        $this->assertSame(
            'go_gc_duration_seconds_sum',
            $this->node->getMetrics()['go_gc_duration_seconds']->metrics[3]->name
        );

        $this->assertSame(
            3,
            $this->node->getMetrics()['go_gc_duration_seconds']->metrics[4]->value
        );

        $this->assertSame(
            'go_gc_duration_seconds_count',
            $this->node->getMetrics()['go_gc_duration_seconds']->metrics[4]->name
        );
    }

    function testSchemaLabels(): void
    {
        $this->assertSame(
            'quantile',
            $this->node->getMetrics()['go_gc_duration_seconds']->metrics[0]->labels[0]->name
        );

        $this->assertSame(
            '0',
            $this->node->getMetrics()['go_gc_duration_seconds']->metrics[0]->labels[0]->value
        );

        $this->assertSame(
            'test',
            $this->node->getMetrics()['go_gc_duration_seconds']->metrics[0]->labels[1]->name
        );

        $this->assertSame(
            '0.25',
            $this->node->getMetrics()['go_gc_duration_seconds']->metrics[0]->labels[1]->value
        );
    }
}
