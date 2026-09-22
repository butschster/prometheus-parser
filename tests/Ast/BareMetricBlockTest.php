<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class BareMetricBlockTest extends TestCase
{
    function testBareMetricBlock(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
bare_metric 42
SCHEMA
        );

        $this->assertArrayHasKey('bare_metric', $node->getMetrics());
        $family = $node->getMetrics()['bare_metric'];
        $this->assertSame('unknown', $family->type);
        $this->assertNull($family->description);
        $this->assertNull($family->unit);
        $this->assertCount(1, $family->metrics);
        $this->assertSame('bare_metric', $family->metrics[0]->name);
        $this->assertSame(42, $family->metrics[0]->value);
    }

    function testBareMetricBlockWithMultipleSamples(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
bare_metric{label="a"} 1.0
bare_metric{label="b"} 2.0
SCHEMA
        );

        $family = $node->getMetrics()['bare_metric'];
        $this->assertCount(2, $family->metrics);
        $this->assertSame(1.0, $family->metrics[0]->value);
        $this->assertSame(2.0, $family->metrics[1]->value);
    }

    function testBareMetricBlockWithTimestamp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
bare_metric 100 1395066363000
SCHEMA
        );

        $family = $node->getMetrics()['bare_metric'];
        $this->assertSame(1395066363000, $family->metrics[0]->timestamp);
    }

    function testBareMetricBlockWithComment(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# A leading comment.
bare_metric 42
SCHEMA
        );

        $this->assertArrayHasKey('bare_metric', $node->getMetrics());
        $family = $node->getMetrics()['bare_metric'];
        $this->assertCount(1, $family->metrics);
        $this->assertSame(42, $family->metrics[0]->value);
    }

    function testBareMetricBlockMixedWithTypedBlock(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
bare_metric 1
# HELP typed_metric A typed metric.
# TYPE typed_metric gauge
typed_metric 2
SCHEMA
        );

        $metrics = $node->getMetrics();
        $this->assertArrayHasKey('bare_metric', $metrics);
        $this->assertArrayHasKey('typed_metric', $metrics);

        $this->assertSame('unknown', $metrics['bare_metric']->type);
        $this->assertSame('gauge', $metrics['typed_metric']->type);
        $this->assertSame(1, $metrics['bare_metric']->metrics[0]->value);
        $this->assertSame(2, $metrics['typed_metric']->metrics[0]->value);
    }

    function testBareMetricBlockWithLabels(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
bare_metric{method="GET", code="200"} 50
SCHEMA
        );

        $metric = $node->getMetrics()['bare_metric']->metrics[0];
        $this->assertSame(50, $metric->value);
        $this->assertCount(2, $metric->labels);
        $this->assertSame('method', $metric->labels[0]->name);
        $this->assertSame('GET', $metric->labels[0]->value);
        $this->assertSame('code', $metric->labels[1]->name);
        $this->assertSame('200', $metric->labels[1]->value);
    }

    function testBareMetricWithEmptyLabelSet(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
bare_metric{} 50
SCHEMA
        );

        $metric = $node->getMetrics()['bare_metric']->metrics[0];
        $this->assertSame(50, $metric->value);
        $this->assertCount(0, $metric->labels);
    }

    /**
     * Metric()+ swallows every consecutive header-less sample into one block
     * whose name comes from the first sample, so unrelated families collapse
     * into one. This is the normal shape of header-less exposition.
     */
    function testBareMetricBlockWithDistinctNames(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
node_cpu_seconds_total 1
node_memory_bytes 2
node_disk_bytes 3
SCHEMA
        );

        $metrics = $node->getMetrics();

        $this->assertCount(3, $metrics);
        $this->assertArrayHasKey('node_cpu_seconds_total', $metrics);
        $this->assertArrayHasKey('node_memory_bytes', $metrics);
        $this->assertArrayHasKey('node_disk_bytes', $metrics);

        $this->assertSame(1, $metrics['node_cpu_seconds_total']->metrics[0]->value);
        $this->assertSame(2, $metrics['node_memory_bytes']->metrics[0]->value);
        $this->assertSame(3, $metrics['node_disk_bytes']->metrics[0]->value);
    }

    function testBareMetricBlockGroupsSamplesOfTheSameName(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
node_cpu_seconds_total{cpu="0"} 1
node_cpu_seconds_total{cpu="1"} 2
node_memory_bytes 3
SCHEMA
        );

        $metrics = $node->getMetrics();

        $this->assertCount(2, $metrics);
        $this->assertCount(2, $metrics['node_cpu_seconds_total']->metrics);
        $this->assertCount(1, $metrics['node_memory_bytes']->metrics);
    }

    function testBareMetricBlockSeparatedByBlankLines(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
node_cpu_seconds_total 1

node_memory_bytes 2
SCHEMA
        );

        $this->assertCount(2, $node->getMetrics());
    }
}
