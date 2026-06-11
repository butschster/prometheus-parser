<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast\CompositeValue;

use Butschster\Prometheus\Tests\Ast\TestCase;

class SummaryValueNodeTest extends TestCase
{
    function testNoQuantiles(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE foo summary
foo {count:17,sum:324789.3,quantile:[]} st@1520430000.123
SCHEMA
        );

        $metric = $node->getMetrics()['foo']->metrics[0];

        $this->assertSame(
            17,
            $metric->value->count
        );

        $this->assertSame(
            324789.3,
            $metric->value->sum
        );

        $this->assertCount(
            0,
            $metric->value->quantile
        );

        $this->assertSame(
            '{count:17,sum:324789.3,quantile:[]}',
            (string)$metric->value
        );

        $this->assertSame(
            1520430000.123,
            $metric->startTimestamp
        );
    }

    function testQuantiles(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE foo summary
foo {count:0,sum:0.0,quantile:[0.95:123.7,0.99:150]} st@1520430000.123
SCHEMA
        );

        $metric = $node->getMetrics()['foo']->metrics[0];

        $this->assertSame(
            0,
            $metric->value->count
        );

        $this->assertSame(
            0.0,
            $metric->value->sum
        );

        $this->assertCount(
            2,
            $metric->value->quantile
        );

        $this->assertSame(
            123.7,
            $metric->value->quantile['0.95']
        );

        $this->assertSame(
            150,
            $metric->value->quantile['0.99']
        );

        $this->assertSame(
            '{count:0,sum:0.0,quantile:[0.95:123.7,0.99:150]}',
            (string)$metric->value
        );

        $this->assertSame(
            1520430000.123,
            $metric->startTimestamp
        );
    }
}
