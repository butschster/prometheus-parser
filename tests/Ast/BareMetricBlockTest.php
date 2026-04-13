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
        $this->assertCount(2, $metric->labels);
        $this->assertSame('method', $metric->labels[0]->name);
        $this->assertSame('GET', $metric->labels[0]->value);
        $this->assertSame('code', $metric->labels[1]->name);
        $this->assertSame('200', $metric->labels[1]->value);
    }
}
