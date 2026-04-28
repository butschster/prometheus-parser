<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class HelpNodeTest extends TestCase
{
    function testHelp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help This describes the metric family.
# TYPE test_help summary
test_help 0
SCHEMA
        );

        $this->assertSame(
            'This describes the metric family.',
            $node->getMetrics()['test_help']->description
        );
    }

    function testEmptyHelp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_empty_help 
# TYPE test_empty_help summary
test_empty_help 0
SCHEMA
        );

        $this->assertNull(
            $node->getMetrics()['test_empty_help']->description
        );
    }

    function testNoHelp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# TYPE test_no_help summary
test_no_help 0
SCHEMA
        );

        $this->assertNull(
            $node->getMetrics()['test_no_help']->description
        );
    }

    function testContainsMetricType(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help counter should not be mistaken for T_METRIC_TYPE
# TYPE test_help summary
test_help 0
# HELP test_help:prefix counters should not be mistaken for T_METRIC_TYPE
# TYPE test_help:prefix summary
test_help:prefix 0
SCHEMA
        );

        $this->assertSame(
            'counter should not be mistaken for T_METRIC_TYPE',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            'counters should not be mistaken for T_METRIC_TYPE',
            $node->getMetrics()['test_help:prefix']->description
        );
    }

    function testContainsMetricName(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help metric_name_like should not be mistaken for T_METRIC_NAME
# TYPE test_help summary
test_help 0
# HELP test_help:prefix metric_name-ish should not be mistaken for T_METRIC_NAME
# TYPE test_help:prefix summary
test_help:prefix 0
SCHEMA
        );

        $this->assertSame(
            'metric_name_like should not be mistaken for T_METRIC_NAME',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            'metric_name-ish should not be mistaken for T_METRIC_NAME',
            $node->getMetrics()['test_help:prefix']->description
        );
    }

    function testContainsFloat(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help 3.14 should not be mistaken for T_FLOAT
# TYPE test_help summary
test_help 0
# HELP test_help:prefix 0.333... should not be mistaken for T_FLOAT
# TYPE test_help:prefix summary
test_help:prefix 0
SCHEMA
        );

        $this->assertSame(
            '3.14 should not be mistaken for T_FLOAT',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            '0.333... should not be mistaken for T_FLOAT',
            $node->getMetrics()['test_help:prefix']->description
        );
    }

    function testContainsInt(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help -1 should not be mistaken for T_INT
# TYPE test_help summary
test_help 0
# HELP test_help:prefix 0day should not be mistaken for T_INT
# TYPE test_help:prefix summary
test_help:prefix 0
SCHEMA
        );

        $this->assertSame(
            '-1 should not be mistaken for T_INT',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            '0day should not be mistaken for T_INT',
            $node->getMetrics()['test_help:prefix']->description
        );
    }

    function testContainsInf(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help +Inf should not be mistaken for T_INF
# TYPE test_help summary
test_help 0
# HELP test_help:prefix +Informal should not be mistaken for T_INF
# TYPE test_help:prefix summary
test_help:prefix 0
SCHEMA
        );

        $this->assertSame(
            '+Inf should not be mistaken for T_INF',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            '+Informal should not be mistaken for T_INF',
            $node->getMetrics()['test_help:prefix']->description
        );
    }

    function testContainsNan(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help NaN should not be mistaken for T_NAN
# TYPE test_help summary
test_help 0
# HELP test_help:prefix Nanny should not be mistaken for T_NAN
# TYPE test_help:prefix summary
test_help:prefix 0
SCHEMA
        );

        $this->assertSame(
            'NaN should not be mistaken for T_NAN',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            'Nanny should not be mistaken for T_NAN',
            $node->getMetrics()['test_help:prefix']->description
        );
    }

    function testContainsEquals(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help = should not be mistaken for T_EQUALS
# TYPE test_help summary
test_help 0
# HELP test_help:prefix =same should not be mistaken for T_EQUALS
# TYPE test_help:prefix summary
test_help:prefix 0
SCHEMA
        );

        $this->assertSame(
            '= should not be mistaken for T_EQUALS',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            '=same should not be mistaken for T_EQUALS',
            $node->getMetrics()['test_help:prefix']->description
        );
    }

    function testContainsColon(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help : should not be mistaken for T_COLON
# TYPE test_help summary
test_help 0
# HELP test_help:suffix this: should not be mistaken for T_COLON
# TYPE test_help:suffix summary
test_help:prefix 0
SCHEMA
        );

        $this->assertSame(
            ': should not be mistaken for T_COLON',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            'this: should not be mistaken for T_COLON',
            $node->getMetrics()['test_help:suffix']->description
        );
    }

    function testContainsHash(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help # should not be mistaken for T_HASH
# TYPE test_help summary
test_help 0
# HELP test_help:prefix #hashtag should not be mistaken for T_HASH
# TYPE test_help:prefix summary
test_help:prefix 0
SCHEMA
        );

        $this->assertSame(
            '# should not be mistaken for T_HASH',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            '#hashtag should not be mistaken for T_HASH',
            $node->getMetrics()['test_help:prefix']->description
        );
    }

    function testContainsBraces(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help {} should not be mistaken for T_LBRACE T_RBRACE
# TYPE test_help summary
test_help 0
# HELP test_help:infix should {also} not be mistaken for T_LBRACE T_RBRACE
# TYPE test_help:infix summary
test_help:infix 0
SCHEMA
        );

        $this->assertSame(
            '{} should not be mistaken for T_LBRACE T_RBRACE',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            'should {also} not be mistaken for T_LBRACE T_RBRACE',
            $node->getMetrics()['test_help:infix']->description
        );
    }

    function testContainsBrackets(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help [] should not be mistaken for T_LBRACKET T_RBRACKET
# TYPE test_help summary
test_help 0
# HELP test_help:infix should [also] not be mistaken for T_LBRACKET T_RBRACKET
# TYPE test_help:infix summary
test_help:infix 0
SCHEMA
        );

        $this->assertSame(
            '[] should not be mistaken for T_LBRACKET T_RBRACKET',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            'should [also] not be mistaken for T_LBRACKET T_RBRACKET',
            $node->getMetrics()['test_help:infix']->description
        );
    }

    function testContainsComma(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help , should not be mistaken for T_COMMA
# TYPE test_help summary
test_help 0
# HELP test_help:suffix And, should not be mistaken for T_COMMA
# TYPE test_help:suffix summary
test_help:suffix 0
SCHEMA
        );

        $this->assertSame(
            ', should not be mistaken for T_COMMA',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            'And, should not be mistaken for T_COMMA',
            $node->getMetrics()['test_help:suffix']->description
        );
    }

    function testContainsStartTimestamp(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help st@ should not be mistaken for T_START_TIMESTAMP
# TYPE test_help summary
test_help 0
# HELP test_help:prefix st@1234 should not be mistaken for T_START_TIMESTAMP
# TYPE test_help:prefix summary
test_help:prefix 0
SCHEMA
        );

        $this->assertSame(
            'st@ should not be mistaken for T_START_TIMESTAMP',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            'st@1234 should not be mistaken for T_START_TIMESTAMP',
            $node->getMetrics()['test_help:prefix']->description
        );
    }

    function testContainsQuotedString(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help "test" should not be mistaken for T_QUOTED_STRING
# TYPE test_help summary
test_help 0
# HELP test_help:prefix "test"ing should not be mistaken for T_QUOTED_STRING
# TYPE test_help:prefix summary
test_help:prefix 0
SCHEMA
        );

        $this->assertSame(
            '"test" should not be mistaken for T_QUOTED_STRING',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            '"test"ing should not be mistaken for T_QUOTED_STRING',
            $node->getMetrics()['test_help:prefix']->description
        );
    }

    function testContainsCompositeValue(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help {count:17,sum:324789.3,quantile:[]} should not be mistaken for CompositeValue()
# TYPE test_help summary
test_help 0
SCHEMA
        );

        $this->assertSame(
            '{count:17,sum:324789.3,quantile:[]} should not be mistaken for CompositeValue()',
            $node->getMetrics()['test_help']->description
        );
    }

    function testContainsCombined(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_help 1 counter should not be mistaken for T_INT T_METRIC_TYPE
# TYPE test_help summary
test_help 0
# HELP test_help:prefix 2 counters should not be mistaken for T_INT T_METRIC_TYPE
# TYPE test_help:prefix summary
test_help:prefix 0
# HELP test_help:no_t_text pure lowercase text matches as all metric_name tokens
# TYPE test_help:no_t_text summary
test_help:no_t_text 0
# HELP test_help:whitespace 0  gauge    should   preserve     whitespace
# TYPE test_help:whitespace summary
test_help:whitespace 0
SCHEMA
        );

        $this->assertSame(
            '1 counter should not be mistaken for T_INT T_METRIC_TYPE',
            $node->getMetrics()['test_help']->description
        );

        $this->assertSame(
            '2 counters should not be mistaken for T_INT T_METRIC_TYPE',
            $node->getMetrics()['test_help:prefix']->description
        );

        $this->assertSame(
            'pure lowercase text matches as all metric_name tokens',
            $node->getMetrics()['test_help:no_t_text']->description
        );

        $this->assertSame(
            '0  gauge    should   preserve     whitespace',
            $node->getMetrics()['test_help:whitespace']->description
        );
    }
}
