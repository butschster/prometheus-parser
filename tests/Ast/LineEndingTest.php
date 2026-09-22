<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class LineEndingTest extends TestCase
{
    /**
     * T_EOR (\r) is declared by the grammar but referenced by no rule and not
     * skipped, so CRLF input cannot be parsed at all.
     */
    function testCarriageReturnLineEndings(): void
    {
        $node = $this->parser->parse("# TYPE test_crlf counter\r\ntest_crlf_total 1\r\n# EOF\r\n");

        $family = $node->getMetrics()['test_crlf'];
        $this->assertSame('counter', $family->type);
        $this->assertSame(1, $family->getTotal()->value);
    }

    function testCarriageReturnLineEndingsWithoutHeaders(): void
    {
        $node = $this->parser->parse("test_crlf 1\r\n");

        $this->assertSame(1, $node->getMetrics()['test_crlf']->metrics[0]->value);
    }

    /**
     * Blank lines between blocks already parse; a leading one must too.
     */
    function testLeadingBlankLine(): void
    {
        $node = $this->parser->parse("\ntest_blank 1\n");

        $this->assertSame(1, $node->getMetrics()['test_blank']->metrics[0]->value);
    }

    function testLeadingBlankLines(): void
    {
        $node = $this->parser->parse("\n\n\n# TYPE test_blank gauge\ntest_blank 1\n");

        $this->assertSame('gauge', $node->getMetrics()['test_blank']->type);
    }

    function testInputOfBlankLinesOnly(): void
    {
        $node = $this->parser->parse("\n\n");

        $this->assertCount(0, $node->getMetrics());
    }
}
