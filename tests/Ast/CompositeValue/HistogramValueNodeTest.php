<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast\CompositeValue;

use Butschster\Prometheus\Tests\Ast\TestCase;

class HistogramValueNodeTest extends TestCase
{
    function testClassicBuckets(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE foo histogram
foo {count:17,sum:324789.3,bucket:[0.0:0,1e-05:0,0.0001:5,0.1:8,1.0:10,10.0:11,100000.0:11,1e+06:15,1e+23:16,1.1e+23:17,+Inf:17]} st@1520430000.123
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
                '0.0' => 0,
                '1e-05' => 0,
                '0.0001' => 5,
                '0.1' => 8,
                '1.0' => 10,
                '10.0' => 11,
                '100000.0' => 11,
                '1e+06' => 15,
                '1e+23' => 16,
                '1.1e+23' => 17,
                '+Inf' => 17,
            ],
            $metric->value->bucket
        );

        $this->assertSame(
            '{count:17,sum:324789.3,bucket:[0.0:0,1e-05:0,0.0001:5,0.1:8,1.0:10,10.0:11,100000.0:11,1e+06:15,1e+23:16,1.1e+23:17,+Inf:17]}',
            (string)$metric->value
        );

        $this->assertSame(
            1520430000.123,
            $metric->startTimestamp
        );
    }

    function testNativeBuckets(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE acme_http_request_seconds histogram
acme_http_request_seconds{path="/api/v1",method="GET"} {count:59,sum:1.2e2,schema:7,zero_threshold:1e-4,zero_count:0,negative_spans:[1:2],negative_buckets:[5,7],positive_spans:[-1:2,3:4],positive_buckets:[5,7,10,9,8,8]} st@1520430000.123
SCHEMA
        );

        $metric = $node->getMetrics()['acme_http_request_seconds']->metrics[0];

        $this->assertSame(
            59,
            $metric->value->count
        );

        $this->assertSame(
            1.2e2,
            $metric->value->sum
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
            '{count:59,sum:1.2e2,schema:7,zero_threshold:1e-4,zero_count:0,negative_spans:[1:2],negative_buckets:[5,7],positive_spans:[-1:2,3:4],positive_buckets:[5,7,10,9,8,8]}',
            (string)$metric->value
        );

        $this->assertSame(
            1520430000.123,
            $metric->startTimestamp
        );
    }

    function testNativeBucketsWithoutBuckets(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE acme_http_request_seconds histogram
acme_http_request_seconds{path="/api/v1",method="GET"} {count:0,sum:0,schema:3,zero_threshold:1e-4,zero_count:0} st@1520430000.123
SCHEMA
        );

        $metric = $node->getMetrics()['acme_http_request_seconds']->metrics[0];

        $this->assertSame(
            0,
            $metric->value->count
        );

        $this->assertSame(
            0,
            $metric->value->sum
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
            '{count:0,sum:0,schema:3,zero_threshold:1e-4,zero_count:0}',
            (string)$metric->value
        );

        $this->assertSame(
            1520430000.123,
            $metric->startTimestamp
        );
    }

    function testClassicAndNativeBuckets(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE acme_http_request_seconds histogram
# UNIT acme_http_request_seconds seconds
# HELP acme_http_request_seconds Latency histogram of all of ACME's HTTP requests.
acme_http_request_seconds{path="/api/v1",method="GET"} {count:2,sum:1.2e2,schema:0,zero_threshold:1e-4,zero_count:0,positive_spans:[1:2],positive_buckets:[1,1],bucket:[0.5:1,1:2,+Inf:2]}
SCHEMA
        );

        $metric = $node->getMetrics()['acme_http_request_seconds']->metrics[0];

        $this->assertSame(
            2,
            $metric->value->count
        );

        $this->assertSame(
            1.2e2,
            $metric->value->sum
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
            '{count:2,sum:1.2e2,schema:0,zero_threshold:1e-4,zero_count:0,positive_spans:[1:2],positive_buckets:[1,1],bucket:[0.5:1,1:2,+Inf:2]}',
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

        $this->assertNull(
            $metric->startTimestamp
        );
    }
}
