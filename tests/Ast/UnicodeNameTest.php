<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class UnicodeNameTest extends TestCase
{
    // -------------------------------------------------------------------------
    //  Unicode label names
    // -------------------------------------------------------------------------

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

    // -------------------------------------------------------------------------
    //  Unicode metric names
    // -------------------------------------------------------------------------

    function testUnicodeMetricName(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
{"my.metric.name"} 5
SCHEMA
        );

        $this->assertArrayHasKey('my.metric.name', $node->getMetrics());
        $metric = $node->getMetrics()['my.metric.name']->metrics[0];
        $this->assertSame(5, $metric->value);
    }

    function testUnicodeMetricNameWithEscapes(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
{"\"my\" metric\n nãme"} 5
SCHEMA
        );

        $this->assertArrayHasKey("\"my\" metric\n nãme", $node->getMetrics());
        $metric = $node->getMetrics()["\"my\" metric\n nãme"]->metrics[0];
        $this->assertSame(5, $metric->value);
    }

    function testUnicodeMetricNameWithRegularLabels(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
{"my.metric.name",label_1="v1",label_2="v2"} 5
SCHEMA
        );

        $this->assertArrayHasKey('my.metric.name', $node->getMetrics());
        $metric = $node->getMetrics()['my.metric.name']->metrics[0];
        $this->assertSame('label_1', $metric->labels[0]->name);
        $this->assertSame('v1', $metric->labels[0]->value);
        $this->assertSame('label_2', $metric->labels[1]->name);
        $this->assertSame('v2', $metric->labels[1]->value);
        $this->assertSame(5, $metric->value);
    }

    function testUnicodeMetricNameWithUnicodeLabels(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
{"my.metric.name","label-with-hyphens"="v1","label.with.dots"="v2"} 5
SCHEMA
        );

        $this->assertArrayHasKey('my.metric.name', $node->getMetrics());
        $metric = $node->getMetrics()['my.metric.name']->metrics[0];
        $this->assertSame('label-with-hyphens', $metric->labels[0]->name);
        $this->assertSame('v1', $metric->labels[0]->value);
        $this->assertSame('label.with.dots', $metric->labels[1]->name);
        $this->assertSame('v2', $metric->labels[1]->value);
        $this->assertSame(5, $metric->value);
    }

    function testUnicodeMetricNameWithMixedLabels(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
{"my.metric.name",label_1="v1","label.2"="v2"} 5
SCHEMA
        );

        $this->assertArrayHasKey('my.metric.name', $node->getMetrics());
        $metric = $node->getMetrics()['my.metric.name']->metrics[0];
        $this->assertSame('label_1', $metric->labels[0]->name);
        $this->assertSame('v1', $metric->labels[0]->value);
        $this->assertSame('label.2', $metric->labels[1]->name);
        $this->assertSame('v2', $metric->labels[1]->value);
        $this->assertSame(5, $metric->value);
    }

    function testUnicodeMetricNameInType(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE "my.metric.name" gauge
{"my.metric.name"} 5
SCHEMA
        );

        $this->assertArrayHasKey('my.metric.name', $node->getMetrics());
        $family = $node->getMetrics()['my.metric.name'];
        $this->assertSame('gauge', $family->type);
        $this->assertSame(5, $family->metrics[0]->value);
    }

    function testUnicodeMetricNameInHelp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP "my.metric.name" A metric with a unicode name.
{"my.metric.name"} 5
SCHEMA
        );

        $family = $node->getMetrics()['my.metric.name'];
        $this->assertSame('A metric with a unicode name.', $family->description);
        $this->assertSame(5, $family->metrics[0]->value);
    }

    function testUnicodeMetricNameInUnit(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# UNIT "my.metric.seconds" seconds
{"my.metric.name"} 5
SCHEMA
        );

        $family = $node->getMetrics()['my.metric.seconds'];
        $this->assertSame('seconds', $family->unit);
        $this->assertSame(5, $family->metrics[0]->value);
    }

    function testRegularLabelNameStillWorks(): void
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
}
