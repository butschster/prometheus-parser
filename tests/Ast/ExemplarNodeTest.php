<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

use Butschster\Prometheus\Ast\ExemplarNode;
use Butschster\Prometheus\Ast\LabelNode;

class ExemplarNodeTest extends TestCase
{
    function testNoExemplar(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_counter counter
test_counter_total 100
SCHEMA
        );

        $this->assertNull(
            $node->getMetrics()['test_counter']->metrics[0]->exemplar
        );
    }

    function testExemplarWithLabelsAndValue(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_counter counter
test_counter_total{code="200"} 100 # {trace_id="abc123"} 1.0
SCHEMA
        );

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplar;

        $this->assertInstanceOf(ExemplarNode::class, $exemplar);
        $this->assertSame(1.0, $exemplar->value);
        $this->assertNull($exemplar->timestamp);
        $this->assertCount(1, $exemplar->labels);
        $this->assertInstanceOf(LabelNode::class, $exemplar->labels[0]);
        $this->assertSame('trace_id', $exemplar->labels[0]->name);
        $this->assertSame('abc123', $exemplar->labels[0]->value);
    }

    function testExemplarWithTimestamp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_counter counter
test_counter_total 1027 1395066363.000 # {trace_id="abc"} 1.0 1395066363.000
SCHEMA
        );

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplar;

        $this->assertInstanceOf(ExemplarNode::class, $exemplar);
        $this->assertSame(1.0, $exemplar->value);
        $this->assertSame(1395066363.000, $exemplar->timestamp);
    }

    function testExemplarWithIntTimestamp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_counter counter
test_counter_total 42 1395066363 # {id="x"} 2 1395066400
SCHEMA
        );

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplar;

        $this->assertInstanceOf(ExemplarNode::class, $exemplar);
        $this->assertSame(2, $exemplar->value);
        $this->assertSame(1395066400, $exemplar->timestamp);
    }

    function testExemplarWithMultipleLabels(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_counter counter
test_counter_total 100 # {trace_id="abc", span_id="def"}  2.5
SCHEMA
        );

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplar;

        $this->assertInstanceOf(ExemplarNode::class, $exemplar);
        $this->assertSame(2.5, $exemplar->value);
        $this->assertCount(2, $exemplar->labels);
        $this->assertSame('trace_id', $exemplar->labels[0]->name);
        $this->assertSame('abc', $exemplar->labels[0]->value);
        $this->assertSame('span_id', $exemplar->labels[1]->name);
        $this->assertSame('def', $exemplar->labels[1]->value);
    }

    function testExemplarIntValue(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_counter counter
test_counter_total 100 # {id="1"} 1
SCHEMA
        );

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplar;

        $this->assertInstanceOf(ExemplarNode::class, $exemplar);
        $this->assertSame(1, $exemplar->value);
    }

    function testExemplarWithInfValue(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_counter counter
test_counter_total 100 # {id="1"} +Inf
SCHEMA
        );

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplar;

        $this->assertInstanceOf(ExemplarNode::class, $exemplar);
        $this->assertInfinite($exemplar->value);
    }

    function testExemplarWithNanValue(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_counter counter
test_counter_total 100 # {id="1"} NaN
SCHEMA
        );

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplar;

        $this->assertInstanceOf(ExemplarNode::class, $exemplar);
        $this->assertNan($exemplar->value);
    }

    function testMetricTimestampAndExemplar(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_counter counter
test_counter_total{code="200"} 1027 1395066363.000 # {trace_id="abc"} 1.0 1395066363.000
SCHEMA
        );

        $metric = $node->getMetrics()['test_counter']->metrics[0];

        $this->assertSame(1395066363.000, $metric->timestamp);
        $this->assertInstanceOf(ExemplarNode::class, $metric->exemplar);
        $this->assertSame(1.0, $metric->exemplar->value);
        $this->assertSame(1395066363.000, $metric->exemplar->timestamp);
    }
}
