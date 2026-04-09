<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class StringTest extends TestCase
{
    function testString(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_string Tests for regular strings.
# TYPE test_string summary
test_string{test_label="label"} 0
# And a comment.
test_string{test_label="another label"} 1
SCHEMA
        );

        $metricFamily = $node->getMetrics()['test_string'];

        $this->assertSame(
            'Tests for regular strings.',
            $metricFamily->description
        );

        $this->assertSame(
            'label',
            $metricFamily->metrics[0]->labels[0]->value
        );

        $this->assertSame(
            'And a comment.',
            $metricFamily->metrics[1]->comment
        );

        $this->assertSame(
            'another label',
            $metricFamily->metrics[1]->labels[0]->value
        );
    }

    function testSpecialCharacters(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_string Tests for strings with 'special characters' such as ?!;:+-*/=_(){}[]#.
# TYPE test_string summary
test_string{test_label="'label' ?!;:+-*/=_(){}[]#"} 0
# And a comment with ?!;:+-*/=_(){}[]#.
test_string{container_url="http://container:8080/path"} 1
SCHEMA
        );

        $metricFamily = $node->getMetrics()['test_string'];

        $this->assertSame(
            "Tests for strings with 'special characters' such as ?!;:+-*/=_(){}[]#.",
            $metricFamily->description
        );

        $this->assertSame(
            "'label' ?!;:+-*/=_(){}[]#",
            $metricFamily->metrics[0]->labels[0]->value
        );

        $this->assertSame(
            'And a comment with ?!;:+-*/=_(){}[]#.',
            $metricFamily->metrics[1]->comment
        );

        $this->assertSame(
            'http://container:8080/path',
            $metricFamily->metrics[1]->labels[0]->value
        );
    }

    function testUnicode(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_string Tests for strings with Unicode characters: αβγ абв אԱა.
# TYPE test_string summary
test_string{test_label="αβγ абв אԱა"} 0
# And a comment about コンニチハ.
test_string{test_label="コンニチハ"} 1
SCHEMA
        );

        $metricFamily = $node->getMetrics()['test_string'];

        $this->assertSame(
            'Tests for strings with Unicode characters: αβγ абв אԱა.',
            $metricFamily->description
        );

        $this->assertSame(
            'αβγ абв אԱა',
            $metricFamily->metrics[0]->labels[0]->value
        );

        $this->assertSame(
            'And a comment about コンニチハ.',
            $metricFamily->metrics[1]->comment
        );

        $this->assertSame(
            'コンニチハ',
            $metricFamily->metrics[1]->labels[0]->value
        );
    }

    function testPrometheusEscapes(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_string Tests for strings with "escapes":\nC:\\DIR\\FILE.TXT
# TYPE test_string summary
test_string{test_label="\"label\"\nC:\\DIR\\FILE.TXT"} 0
# And a "comment":\nC:\\DIR\\FILE.TXT
test_string 1
SCHEMA
        );

        $metricFamily = $node->getMetrics()['test_string'];

        $this->assertSame(
            'Tests for strings with "escapes":
C:\DIR\FILE.TXT',
            $metricFamily->description
        );

        $this->assertSame(
            '"label"
C:\DIR\FILE.TXT',
            $metricFamily->metrics[0]->labels[0]->value
        );

        $this->assertSame(
            'And a "comment":
C:\DIR\FILE.TXT',
            $metricFamily->metrics[1]->comment
        );
    }

    function testOpenMetricsEscapes(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_string Tests for strings with \"escapes\":\nC:\\DIR\\FILE.TXT
# TYPE test_string summary
test_string{test_label="\"label\"\nC:\\DIR\\FILE.TXT"} 0
SCHEMA
        );

        $metricFamily = $node->getMetrics()['test_string'];

        $this->assertSame(
            'Tests for strings with "escapes":
C:\DIR\FILE.TXT',
            $metricFamily->description
        );

        $this->assertSame(
            '"label"
C:\DIR\FILE.TXT',
            $metricFamily->metrics[0]->labels[0]->value
        );
    }
}
