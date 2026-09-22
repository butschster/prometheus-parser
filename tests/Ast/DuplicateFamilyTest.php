<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class DuplicateFamilyTest extends TestCase
{
    /**
     * Two blocks declaring the same family name must not lose samples: the
     * schema keys families by name, so the second block used to replace the
     * first one outright.
     */
    function testSamplesOfRepeatedFamilyNameAreKept(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE http_requests counter
http_requests_total 1
# TYPE http_requests counter
http_requests_created 1520430000.123
SCHEMA
        );

        $family = $node->getMetrics()['http_requests'];

        $this->assertCount(2, $family->metrics);
        $this->assertNotNull($family->getTotal());
        $this->assertNotNull($family->getCreated());
    }

    function testRepeatedFamilyNameKeepsHeadersOfBothBlocks(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE http_requests counter
# HELP http_requests The total number of requests.
http_requests_total 1
# UNIT http_requests requests
http_requests_created 1520430000.123
SCHEMA
        );

        $family = $node->getMetrics()['http_requests'];

        $this->assertSame('counter', $family->type);
        $this->assertSame('The total number of requests.', $family->description);
        $this->assertSame('requests', $family->unit);
    }
}
