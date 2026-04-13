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
    //  Unicode metric names in TYPE / HELP / UNIT directives
    // -------------------------------------------------------------------------

    function testUnicodeMetricNameInType(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE "my.metric.name" gauge
SCHEMA
        );

        $this->assertArrayHasKey('my.metric.name', $node->getMetrics());
        $family = $node->getMetrics()['my.metric.name'];
        $this->assertSame('gauge', $family->type);
    }

    function testUnicodeMetricNameInHelp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP "my.metric.name" A metric with a unicode name.
# TYPE "my.metric.name" gauge
SCHEMA
        );

        $family = $node->getMetrics()['my.metric.name'];
        $this->assertSame('A metric with a unicode name.', $family->description);
    }

    function testUnicodeMetricNameInUnit(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE "my.metric.seconds" gauge
# UNIT "my.metric.seconds" seconds
SCHEMA
        );

        $family = $node->getMetrics()['my.metric.seconds'];
        $this->assertSame('seconds', $family->unit);
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
