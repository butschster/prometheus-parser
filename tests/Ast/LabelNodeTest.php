<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class LabelNodeTest extends TestCase
{
    function testLabel(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_metric gauge
test_metric{label_name="value"} 1
SCHEMA
        );

        $metric = $node->getMetrics()['test_metric']->metrics[0];

        $this->assertSame('label_name', $metric->labels[0]->name);
        $this->assertSame('value', $metric->labels[0]->value);
    }

    function testMultipleLabels(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_metric gauge
test_metric{label_1="v1", label_2="v2"} 1
SCHEMA
        );

        $metric = $node->getMetrics()['test_metric']->metrics[0];

        $this->assertSame('label_1', $metric->labels[0]->name);
        $this->assertSame('v1', $metric->labels[0]->value);
        $this->assertSame('label_2', $metric->labels[1]->name);
        $this->assertSame('v2', $metric->labels[1]->value);
    }

    function testConfusableLabelNames(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_metric Test that these label names aren't misinterpreted as other tokens
test_metric{count="T_COUNT", counter="T_METRIC_TYPE", nano="T_NAN + o", infra="T_INF + ra"} 1
SCHEMA
        );

        $metric = $node->getMetrics()['test_metric']->metrics[0];

        $this->assertSame('count', $metric->labels[0]->name);
        $this->assertSame('counter', $metric->labels[1]->name);
        $this->assertSame('nano', $metric->labels[2]->name);
        $this->assertSame('infra', $metric->labels[3]->name);
    }

    function testUnicodeLabelName(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_metric gauge
test_metric{"unicode.label"="value"} 1
SCHEMA
        );

        $metric = $node->getMetrics()['test_metric']->metrics[0];

        $this->assertSame('unicode.label', $metric->labels[0]->name);
        $this->assertSame('value', $metric->labels[0]->value);
    }

    function testUnicodeLabelNameWithEscapes(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_metric gauge
test_metric{"unicode\n\"låbel\""="value"} 1
SCHEMA
        );

        $metric = $node->getMetrics()['test_metric']->metrics[0];

        $this->assertSame("unicode\n\"låbel\"", $metric->labels[0]->name);
        $this->assertSame('value', $metric->labels[0]->value);
    }

    function testUnicodeLabelNameMixedWithRegular(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_metric gauge
test_metric{"my.label"="foo", regular_label="bar"} 2
SCHEMA
        );

        $metric = $node->getMetrics()['test_metric']->metrics[0];

        $this->assertSame('my.label', $metric->labels[0]->name);
        $this->assertSame('foo', $metric->labels[0]->value);
        $this->assertSame('regular_label', $metric->labels[1]->name);
        $this->assertSame('bar', $metric->labels[1]->value);
    }

    function testUnicodeLabelNameWithSpecialChars(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_metric gauge
test_metric{"label-with-hyphens"="v1", "label.with.dots"="v2"} 3
SCHEMA
        );

        $metric = $node->getMetrics()['test_metric']->metrics[0];

        $this->assertSame('label-with-hyphens', $metric->labels[0]->name);
        $this->assertSame('v1', $metric->labels[0]->value);
        $this->assertSame('label.with.dots', $metric->labels[1]->name);
        $this->assertSame('v2', $metric->labels[1]->value);
    }
}
