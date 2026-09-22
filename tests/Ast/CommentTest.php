<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class CommentTest extends TestCase
{
    /**
     * Every MetricData alternative needs a header or at least one sample, so a
     * comment that follows the last sample used to abort the parse.
     */
    function testTrailingComment(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
test_comment 1
# A comment after the last sample.
SCHEMA
        );

        $this->assertSame(1, $node->getMetrics()['test_comment']->metrics[0]->value);
    }

    function testTrailingCommentBeforeEof(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
test_comment 1
# A comment after the last sample.
# EOF

SCHEMA
        );

        $this->assertTrue($node->eof);
        $this->assertSame(1, $node->getMetrics()['test_comment']->metrics[0]->value);
    }

    function testInputOfCommentsOnly(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# Just a comment.
# And another one.
SCHEMA
        );

        $this->assertCount(0, $node->getMetrics());
    }

    /**
     * The spec only requires # to be the first non-whitespace character.
     */
    function testCommentWithoutSpaceAfterHash(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
#comment
test_comment 1
SCHEMA
        );

        $this->assertSame(1, $node->getMetrics()['test_comment']->metrics[0]->value);
    }

    function testEmptyComment(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
#
test_comment 1
SCHEMA
        );

        $this->assertSame(1, $node->getMetrics()['test_comment']->metrics[0]->value);
    }

    /**
     * A comment is attached to the sample that follows it. In a header-less
     * block the leading comment is matched by MetricData instead of Metric, and
     * MetricDataNode drops it, so attachment is inconsistent with the same
     * comment placed after a first sample.
     */
    function testLeadingCommentIsAttachedToTheFollowingSample(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# A leading comment.
test_comment 1
SCHEMA
        );

        $this->assertSame(
            'A leading comment.',
            $node->getMetrics()['test_comment']->metrics[0]->comment
        );
    }

    function testCommentBetweenSamplesIsAttachedToTheFollowingSample(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_comment gauge
test_comment{a="1"} 1
# An inner comment.
test_comment{a="2"} 2
SCHEMA
        );

        $metrics = $node->getMetrics()['test_comment']->metrics;

        $this->assertNull($metrics[0]->comment);
        $this->assertSame('An inner comment.', $metrics[1]->comment);
    }
}
