<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class MetricDataNodeTest extends TestCase
{
    function testFamilyWithoutSamples(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE empty_family counter
# HELP empty_family A family that has been declared but has no samples yet.
# EOF

SCHEMA
        );

        $family = $node->getMetrics()['empty_family'];

        $this->assertSame('empty_family', $family->name);
        $this->assertSame('counter', $family->type);
        $this->assertSame([], $family->metrics);
    }

    function testFamilyWithoutSamplesIsIterable(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE empty_family counter
# EOF

SCHEMA
        );

        $this->assertCount(0, \iterator_to_array($node->getMetrics()['empty_family']));
    }

    /**
     * The sub-metric accessors must work on a family that carries no samples.
     */
    function testSubMetricAccessorsOnFamilyWithoutSamples(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE empty_family histogram
# UNIT empty_family seconds
# EOF

SCHEMA
        );

        $family = $node->getMetrics()['empty_family'];

        $this->assertNull($family->getTotal());
        $this->assertNull($family->getCreated());
        $this->assertNull($family->getSum());
        $this->assertNull($family->getCount());
        $this->assertNull($family->getGSum());
        $this->assertNull($family->getGCount());
        $this->assertSame([], $family->getBuckets());
    }

    /**
     * A sample-less family declared before a populated one must not affect it.
     */
    function testFamilyWithoutSamplesFollowedByPopulatedFamily(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE empty_family counter
# TYPE filled_family counter
filled_family_total 7
# EOF

SCHEMA
        );

        $this->assertSame([], $node->getMetrics()['empty_family']->metrics);
        $this->assertSame(7, $node->getMetrics()['filled_family']->getTotal()->value);
    }
}
