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

        $this->assertCount(0, $node->getMetrics()['test_counter']->metrics[0]->exemplars);
    }

    function testExemplarWithLabelsAndValue(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_counter counter
test_counter_total{code="200"} 100 # {trace_id="abc123"} 1.0
SCHEMA
        );

        $this->assertCount(1, $node->getMetrics()['test_counter']->metrics[0]->exemplars);

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplars[0];

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

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplars[0];

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

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplars[0];

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

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplars[0];

        $this->assertInstanceOf(ExemplarNode::class, $exemplar);
        $this->assertSame(2.5, $exemplar->value);
        $this->assertCount(2, $exemplar->labels);
        $this->assertSame('trace_id', $exemplar->labels[0]->name);
        $this->assertSame('abc', $exemplar->labels[0]->value);
        $this->assertSame('span_id', $exemplar->labels[1]->name);
        $this->assertSame('def', $exemplar->labels[1]->value);
    }

    function testExemplarWithEmptyLabelSet(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_counter counter
test_counter_total 100 # {}  2.5
SCHEMA
        );

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplars[0];

        $this->assertInstanceOf(ExemplarNode::class, $exemplar);
        $this->assertSame(2.5, $exemplar->value);
        $this->assertCount(0, $exemplar->labels);
    }

    function testExemplarIntValue(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_counter counter
test_counter_total 100 # {id="1"} 1
SCHEMA
        );

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplars[0];

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

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplars[0];

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

        $exemplar = $node->getMetrics()['test_counter']->metrics[0]->exemplars[0];

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
        $this->assertCount(1, $metric->exemplars);
        $this->assertInstanceOf(ExemplarNode::class, $metric->exemplars[0]);
        $this->assertSame(1.0, $metric->exemplars[0]->value);
        $this->assertSame(1395066363.000, $metric->exemplars[0]->timestamp);
    }

    function testMetricMultipleExemplars(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE foo histogram
foo {count:17,sum:324789.3,schema:0,zero_threshold:1e-4,zero_count:0,positive_spans:[0:2],positive_buckets:[5,12]} st@1520430000.123 # {trace_id="shaZ8oxi"} 0.67 1520879607.789 # {trace_id="ookahn0M"} 1.2 1520879608.589
SCHEMA
        );

        $metric = $node->getMetrics()['foo']->metrics[0];

        $this->assertSame(1520430000.123, $metric->startTimestamp);
        $this->assertCount(2, $metric->exemplars);
        $this->assertInstanceOf(ExemplarNode::class, $metric->exemplars[0]);
        $this->assertSame(0.67, $metric->exemplars[0]->value);
        $this->assertSame(1520879607.789, $metric->exemplars[0]->timestamp);
        $this->assertInstanceOf(ExemplarNode::class, $metric->exemplars[1]);
        $this->assertSame(1.2, $metric->exemplars[1]->value);
        $this->assertSame(1520879608.589, $metric->exemplars[1]->timestamp);
    }
}
