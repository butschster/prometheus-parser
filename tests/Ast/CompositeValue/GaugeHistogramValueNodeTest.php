<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast\CompositeValue;

use Butschster\Prometheus\Tests\Ast\TestCase;

class GaugeHistogramValueNodeTest extends TestCase
{
    function testClassicBuckets(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE foo gaugehistogram
foo {gcount:42,gsum:3289.3,bucket:[0.01:20,0.1:25,1:34,+Inf:42]}
SCHEMA
        );

        $metric = $node->getMetrics()['foo']->metrics[0];

        $this->assertSame(
            42,
            $metric->value->gcount
        );

        $this->assertSame(
            3289.3,
            $metric->value->gsum
        );

        $this->assertNull(
            $metric->value->negative_spans
        );

        $this->assertNull(
            $metric->value->negative_buckets
        );

        $this->assertNull(
            $metric->value->positive_spans
        );

        $this->assertNull(
            $metric->value->positive_buckets
        );

        $this->assertSame(
            [
                '0.01' => 20,
                '0.1' => 25,
                '1' => 34,
                '+Inf' => 42,
            ],
            $metric->value->bucket
        );

        $this->assertSame(
            '{gcount:42,gsum:3289.3,bucket:[0.01:20,0.1:25,1:34,+Inf:42]}',
            (string)$metric->value
        );
    }

    function testNativeBuckets(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE acme_http_request_seconds gaugehistogram
acme_http_request_seconds{path="/api/v1",method="GET"} {gcount:59,gsum:1.2e2,schema:7,zero_threshold:1e-4,zero_count:0,negative_spans:[1:2],negative_buckets:[5,7],positive_spans:[-1:2,3:4],positive_buckets:[5,7,10,9,8,8]}
SCHEMA
        );

        $metric = $node->getMetrics()['acme_http_request_seconds']->metrics[0];

        $this->assertSame(
            59,
            $metric->value->gcount
        );

        $this->assertSame(
            1.2e2,
            $metric->value->gsum
        );

        $this->assertSame(
            7,
            $metric->value->schema
        );

        $this->assertSame(
            1e-4,
            $metric->value->zero_threshold
        );

        $this->assertSame(
            0,
            $metric->value->zero_count
        );

        $this->assertSame(
            [1 => 2],
            $metric->value->negative_spans
        );

        $this->assertSame(
            [5, 7],
            $metric->value->negative_buckets
        );

        $this->assertSame(
            [-1 => 2, 3 => 4],
            $metric->value->positive_spans
        );

        $this->assertSame(
            [5, 7, 10, 9, 8, 8],
            $metric->value->positive_buckets
        );

        $this->assertNull(
            $metric->value->bucket
        );

        $this->assertSame(
            '{gcount:59,gsum:1.2e2,schema:7,zero_threshold:1e-4,zero_count:0,negative_spans:[1:2],negative_buckets:[5,7],positive_spans:[-1:2,3:4],positive_buckets:[5,7,10,9,8,8]}',
            (string)$metric->value
        );
    }

    function testNativeBucketsWithoutBuckets(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE acme_http_request_seconds gaugehistogram
acme_http_request_seconds{path="/api/v1",method="GET"} {gcount:0,gsum:0,schema:3,zero_threshold:1e-4,zero_count:0}
SCHEMA
        );

        $metric = $node->getMetrics()['acme_http_request_seconds']->metrics[0];

        $this->assertSame(
            0,
            $metric->value->gcount
        );

        $this->assertSame(
            0,
            $metric->value->gsum
        );

        $this->assertSame(
            3,
            $metric->value->schema
        );

        $this->assertSame(
            1e-4,
            $metric->value->zero_threshold
        );

        $this->assertSame(
            0,
            $metric->value->zero_count
        );

        $this->assertNull(
            $metric->value->negative_spans
        );

        $this->assertNull(
            $metric->value->negative_buckets
        );

        $this->assertNull(
            $metric->value->positive_spans
        );

        $this->assertNull(
            $metric->value->positive_buckets
        );

        $this->assertNull(
            $metric->value->bucket
        );

        $this->assertSame(
            '{gcount:0,gsum:0,schema:3,zero_threshold:1e-4,zero_count:0}',
            (string)$metric->value
        );
    }

    function testClassicAndNativeBuckets(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE acme_http_request_seconds gaugehistogram
# UNIT acme_http_request_seconds seconds
# HELP acme_http_request_seconds Latency histogram of all of ACME's HTTP requests.
acme_http_request_seconds{path="/api/v1",method="GET"} {gcount:2,gsum:1.2e2,schema:0,zero_threshold:1e-4,zero_count:0,positive_spans:[1:2],positive_buckets:[1,1],bucket:[0.5:1,1:2,+Inf:2]}
SCHEMA
        );

        $metric = $node->getMetrics()['acme_http_request_seconds']->metrics[0];

        $this->assertSame(
            2,
            $metric->value->gcount
        );

        $this->assertSame(
            1.2e2,
            $metric->value->gsum
        );

        $this->assertSame(
            0,
            $metric->value->schema
        );

        $this->assertSame(
            1e-4,
            $metric->value->zero_threshold
        );

        $this->assertSame(
            0,
            $metric->value->zero_count
        );

        $this->assertSame(
            [1 => 2],
            $metric->value->positive_spans
        );

        $this->assertSame(
            [1, 1],
            $metric->value->positive_buckets
        );

        $this->assertSame(
            '{gcount:2,gsum:1.2e2,schema:0,zero_threshold:1e-4,zero_count:0,positive_spans:[1:2],positive_buckets:[1,1],bucket:[0.5:1,1:2,+Inf:2]}',
            (string)$metric->value
        );

        $this->assertSame(
            [
                '0.5' => 1,
                '1' => 2,
                '+Inf' => 2,
            ],
            $metric->value->bucket
        );
    }
}
