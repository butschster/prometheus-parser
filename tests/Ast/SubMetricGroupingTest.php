<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class SubMetricGroupingTest extends TestCase
{
    // -------------------------------------------------------------------------
    //  Counter
    // -------------------------------------------------------------------------

    function testCounterTotal(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE requests counter
requests_total 1027
SCHEMA
        );

        $family = $node->getMetrics()['requests'];
        $this->assertSame(1027, $family->getTotal()->value);
        $this->assertNull($family->getCreated());
    }

    function testCounterTotalAndCreated(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE requests counter
requests_total 1027
requests_created 1520430000.123
SCHEMA
        );

        $family = $node->getMetrics()['requests'];
        $this->assertSame(1027, $family->getTotal()->value);
        $this->assertSame(1520430000.123, $family->getCreated()->value);
    }

    function testCounterNoTotalReturnsNull(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE requests counter
requests_other 1027
SCHEMA
        );

        $family = $node->getMetrics()['requests'];
        $this->assertNull($family->getTotal());
    }

    // -------------------------------------------------------------------------
    //  Summary
    // -------------------------------------------------------------------------

    function testSummarySubMetrics(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE rpc_duration summary
rpc_duration{quantile="0.5"} 4773
rpc_duration_sum 1.7560473e+07
rpc_duration_count 2693
rpc_duration_created 1520430000.0
SCHEMA
        );

        $family = $node->getMetrics()['rpc_duration'];
        $this->assertSame(1.7560473e+07, $family->getSum()->value);
        $this->assertSame(2693, $family->getCount()->value);
        $this->assertSame(1520430000.0, $family->getCreated()->value);
        // Non-sub-metric samples are still accessible via metrics[]
        $this->assertCount(4, $family->metrics);
    }

    // -------------------------------------------------------------------------
    //  Histogram
    // -------------------------------------------------------------------------

    function testHistogramSubMetrics(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE http_request_duration histogram
http_request_duration_bucket{le="0.05"} 24054
http_request_duration_bucket{le="0.1"} 33444
http_request_duration_bucket{le="+Inf"} 144320
http_request_duration_sum 53423
http_request_duration_count 144320
http_request_duration_created 1520430000.0
SCHEMA
        );

        $family = $node->getMetrics()['http_request_duration'];

        $buckets = $family->getBuckets();
        $this->assertCount(3, $buckets);
        $this->assertSame(24054, $buckets[0]->value);
        $this->assertSame(144320, $buckets[2]->value);

        $this->assertSame(53423, $family->getSum()->value);
        $this->assertSame(144320, $family->getCount()->value);
        $this->assertSame(1520430000.0, $family->getCreated()->value);
    }

    function testHistogramNoBuckets(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE http_request_duration histogram
http_request_duration_sum 53423
http_request_duration_count 144320
SCHEMA
        );

        $family = $node->getMetrics()['http_request_duration'];
        $this->assertSame([], $family->getBuckets());
    }

    // -------------------------------------------------------------------------
    //  GaugeHistogram
    // -------------------------------------------------------------------------

    function testGaugeHistogramSubMetrics(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE http_request_size gaugehistogram
http_request_size_bucket{le="100"} 50
http_request_size_bucket{le="+Inf"} 100
http_request_size_gsum 5000
http_request_size_gcount 100
SCHEMA
        );

        $family = $node->getMetrics()['http_request_size'];

        $this->assertCount(2, $family->getBuckets());
        $this->assertSame(5000, $family->getGSum()->value);
        $this->assertSame(100, $family->getGCount()->value);
        $this->assertNull($family->getSum());
        $this->assertNull($family->getCount());
    }

    // -------------------------------------------------------------------------
    //  Absent sub-metrics return null
    // -------------------------------------------------------------------------

    function testAbsentSubMetricsReturnNull(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_gauge gauge
test_gauge 1
SCHEMA
        );

        $family = $node->getMetrics()['test_gauge'];

        $this->assertNull($family->getTotal());
        $this->assertNull($family->getCreated());
        $this->assertNull($family->getSum());
        $this->assertNull($family->getCount());
        $this->assertNull($family->getGSum());
        $this->assertNull($family->getGCount());
        $this->assertSame([], $family->getBuckets());
    }
}
