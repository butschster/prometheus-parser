<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class CompositeValueTest extends TestCase
{
    /**
     * Native histogram spans are an ordered list of (offset, length) pairs.
     * Offsets are deltas, so the same offset may appear more than once and
     * no span may be dropped or reordered.
     */
    function testPositiveSpansKeepOrderAndDuplicateOffsets(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_native_histogram histogram
test_native_histogram {count:10,sum:1.2e2,schema:0,zero_threshold:1e-4,zero_count:0,positive_spans:[0:2,0:3,2:5],positive_buckets:[1,2,3,4,5,6,7,8,9,10]}
SCHEMA
        );

        $value = $node->getMetrics()['test_native_histogram']->metrics[0]->value;

        $this->assertSame([[0, 2], [0, 3], [2, 5]], $value->positive_spans);
    }

    function testNegativeSpansKeepOrderAndDuplicateOffsets(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_native_histogram histogram
test_native_histogram {count:10,sum:1.2e2,schema:0,zero_threshold:1e-4,zero_count:0,negative_spans:[-1:2,-1:3],negative_buckets:[1,2,3,4,5]}
SCHEMA
        );

        $value = $node->getMetrics()['test_native_histogram']->metrics[0]->value;

        $this->assertSame([[-1, 2], [-1, 3]], $value->negative_spans);
    }

    function testNativeHistogramScalars(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_native_histogram histogram
test_native_histogram {count:10,sum:1.2e2,schema:3,zero_threshold:1e-4,zero_count:2}
SCHEMA
        );

        $value = $node->getMetrics()['test_native_histogram']->metrics[0]->value;

        $this->assertSame(10, $value->count);
        $this->assertSame(120.0, $value->sum);
        $this->assertSame(3, $value->schema);
        $this->assertSame(0.0001, $value->zero_threshold);
        $this->assertSame(2, $value->zero_count);
    }

    /**
     * The classic bucket form carries no native histogram fields, so they must
     * read as null just like the other optional fields do.
     */
    function testClassicHistogramLeavesNativeFieldsNull(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_classic_histogram histogram
test_classic_histogram {count:2,sum:2.5,bucket:[0.5:1,+Inf:2]}
SCHEMA
        );

        $value = $node->getMetrics()['test_classic_histogram']->metrics[0]->value;

        $this->assertSame(2, $value->count);
        $this->assertSame(2.5, $value->sum);
        $this->assertCount(2, $value->bucket);
        $this->assertNull($value->schema);
        $this->assertNull($value->zero_threshold);
        $this->assertNull($value->zero_count);
        $this->assertNull($value->positive_spans);
        $this->assertNull($value->positive_buckets);
        $this->assertNull($value->negative_spans);
        $this->assertNull($value->negative_buckets);
    }

    function testClassicGaugeHistogramLeavesNativeFieldsNull(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_classic_gaugehistogram gaugehistogram
test_classic_gaugehistogram {gcount:2,gsum:2.5,bucket:[0.5:1,+Inf:2]}
SCHEMA
        );

        $value = $node->getMetrics()['test_classic_gaugehistogram']->metrics[0]->value;

        $this->assertSame(2, $value->gcount);
        $this->assertSame(2.5, $value->gsum);
        $this->assertNull($value->schema);
        $this->assertNull($value->zero_threshold);
        $this->assertNull($value->zero_count);
    }
}
