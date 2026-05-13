<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class MetricNameNodeTest extends TestCase
{
    function testMetricName(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
test_metric 5
SCHEMA
        );

        $this->assertArrayHasKey('test_metric', $node->getMetrics());
        $metric = $node->getMetrics()['test_metric']->metrics[0];
        $this->assertSame(5, $metric->value);
    }

    function testConfusableMetricNames(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP sum_test_metric Test that this metric name isn't misinterpreted as starting with T_SUM
sum_test_metric 0

# HELP info_test_metric Test that this metric name isn't misinterpreted as starting with T_METRIC_TYPE
info_test_metric 1

# HELP nano_test_metric Test that this metric name isn't misinterpreted as starting with T_NAN
nano_test_metric 2

# HELP infra_test_metric Test that this metric name isn't misinterpreted as starting with T_INF
infra_test_metric 3
SCHEMA
        );

        $this->assertArrayHasKey('sum_test_metric', $node->getMetrics());
        $metric = $node->getMetrics()['sum_test_metric']->metrics[0];
        $this->assertSame(0, $metric->value);

        $this->assertArrayHasKey('info_test_metric', $node->getMetrics());
        $metric = $node->getMetrics()['info_test_metric']->metrics[0];
        $this->assertSame(1, $metric->value);

        $this->assertArrayHasKey('nano_test_metric', $node->getMetrics());
        $metric = $node->getMetrics()['nano_test_metric']->metrics[0];
        $this->assertSame(2, $metric->value);

        $this->assertArrayHasKey('infra_test_metric', $node->getMetrics());
        $metric = $node->getMetrics()['infra_test_metric']->metrics[0];
        $this->assertSame(3, $metric->value);
    }

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
{"my.metric.seconds"} 5
SCHEMA
        );

        $family = $node->getMetrics()['my.metric.seconds'];
        $this->assertSame('seconds', $family->unit);
        $this->assertSame(5, $family->metrics[0]->value);
    }
}
