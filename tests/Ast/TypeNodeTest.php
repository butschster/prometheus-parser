<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

use Butschster\Prometheus\Exceptions\UnexpectedTokenException;

class TypeNodeTest extends TestCase
{
    /**
     * @testWith ["summary"]
     *           ["counter"]
     *           ["gauge"]
     *           ["histogram"]
     *           ["gaugehistogram"]
     *           ["stateset"]
     *           ["info"]
     *           ["unknown"]
     *           ["untyped"]
     */
    function testType(string $type): void
    {
        $node = $this->parser->parse(<<<SCHEMA
# TYPE test_type $type
test_type 0
SCHEMA
        );

        $this->assertSame(
            $type,
            $node->getMetrics()['test_type']->type
        );
    }

    function testInvalidTypeThrowsException(): void
    {
        $this->expectException(UnexpectedTokenException::class);
        $this->parser->parse(<<<'SCHEMA'
# TYPE test_type foobar
test_type 0
SCHEMA
        );
    }

    /**
     * MetricName() admits every token that is a syntactically valid name, so a
     * family may be named after one of the composite value keywords.
     *
     * @testWith ["count"]
     *           ["gcount"]
     *           ["sum"]
     *           ["gsum"]
     *           ["quantile"]
     *           ["bucket"]
     *           ["schema"]
     *           ["zero_count"]
     *           ["zero_threshold"]
     *           ["negative_spans"]
     *           ["negative_buckets"]
     *           ["positive_spans"]
     *           ["positive_buckets"]
     *           ["nan"]
     */
    function testKeywordAsMetricName(string $name): void
    {
        $node = $this->parser->parse(<<<SCHEMA
# TYPE $name gauge
$name 1
SCHEMA
        );

        $family = $node->getMetrics()[$name];
        $this->assertSame($name, $family->name);
        $this->assertSame('gauge', $family->type);
    }

    /**
     * A family may also be named after a metric type keyword; the name and the
     * type are then the same kind of token and must be told apart by position.
     *
     * @testWith ["info"]
     *           ["gauge"]
     *           ["counter"]
     *           ["summary"]
     *           ["histogram"]
     *           ["gaugehistogram"]
     *           ["stateset"]
     *           ["unknown"]
     *           ["untyped"]
     */
    function testMetricTypeKeywordAsMetricName(string $name): void
    {
        $node = $this->parser->parse(<<<SCHEMA
# TYPE $name gauge
$name 1
SCHEMA
        );

        $family = $node->getMetrics()[$name];
        $this->assertSame($name, $family->name);
        $this->assertSame('gauge', $family->type);
    }
}
