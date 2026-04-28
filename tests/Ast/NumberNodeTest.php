<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

class NumberNodeTest extends TestCase
{
    function testInt(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_int Tests for handling integer numbers.
# TYPE test_int summary
test_int 0
test_int +0
test_int -0
test_int 1
test_int +1
test_int -1
test_int 42
test_int +42
test_int -42
test_int 042
test_int +042
test_int -042
SCHEMA
        );

        $metrics = $node->getMetrics()['test_int']->metrics;

        $this->assertSame(
            0,
            $metrics[0]->value
        );

        $this->assertSame(
            0,
            $metrics[1]->value
        );

        $this->assertSame(
            0,
            $metrics[2]->value
        );

        $this->assertSame(
            1,
            $metrics[3]->value
        );

        $this->assertSame(
            1,
            $metrics[4]->value
        );

        $this->assertSame(
            -1,
            $metrics[5]->value
        );

        $this->assertSame(
            42,
            $metrics[6]->value
        );

        $this->assertSame(
            42,
            $metrics[7]->value
        );

        $this->assertSame(
            -42,
            $metrics[8]->value
        );

        $this->assertSame(
            42,
            $metrics[9]->value
        );

        $this->assertSame(
            42,
            $metrics[10]->value
        );

        $this->assertSame(
            -42,
            $metrics[11]->value
        );
    }

    function testFloat(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_float Tests for handling floating point numbers.
# TYPE test_float summary
test_float 0.0
test_float +0.0
test_float -0.0
test_float .33
test_float +.33
test_float -.33
test_float 42.5
test_float +42.5
test_float -42.5
test_float 042.5
test_float +042.5
test_float -042.5
SCHEMA
        );

        $metrics = $node->getMetrics()['test_float']->metrics;

        $this->assertSame(
            0.0,
            $metrics[0]->value
        );

        $this->assertSame(
            0.0,
            $metrics[1]->value
        );

        $this->assertSame(
            0.0,
            $metrics[2]->value
        );

        $this->assertSame(
            0.33,
            $metrics[3]->value
        );

        $this->assertSame(
            0.33,
            $metrics[4]->value
        );

        $this->assertSame(
            -0.33,
            $metrics[5]->value
        );

        $this->assertSame(
            42.5,
            $metrics[6]->value
        );

        $this->assertSame(
            42.5,
            $metrics[7]->value
        );

        $this->assertSame(
            -42.5,
            $metrics[8]->value
        );

        $this->assertSame(
            42.5,
            $metrics[9]->value
        );

        $this->assertSame(
            42.5,
            $metrics[10]->value
        );

        $this->assertSame(
            -42.5,
            $metrics[11]->value
        );
    }

    function testExponentialNotation(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_exp Tests for handling exponential notation.
# TYPE test_exp summary
test_exp 3e5
test_exp 3E5
test_exp +3e5
test_exp +3E5
test_exp -3e5
test_exp -3E5
test_exp .3e5
test_exp .3E5
test_exp +.3e5
test_exp +.3E5
test_exp -.3e5
test_exp -.3E5
test_exp 3.332e5
test_exp +3.332e5
test_exp 3.332e+5
test_exp 3.332e+05
test_exp 3.332e-5
test_exp 3.332e-05
test_exp -3.332e5
test_exp -3.332e+5
test_exp -3.332e+05
test_exp -3.332e-5
test_exp -3.332e-05
SCHEMA
        );

        $metrics = $node->getMetrics()['test_exp']->metrics;

        foreach (range(0, 3) as $i) {
            $this->assertSame(
                3e5,
                $metrics[$i]->value
            );
        }

        foreach (range(4, 5) as $i) {
            $this->assertSame(
                -3e5,
                $metrics[$i]->value
            );
        }

        foreach (range(6, 9) as $i) {
            $this->assertSame(
                0.3e5,
                $metrics[$i]->value
            );
        }

        foreach (range(10, 11) as $i) {
            $this->assertSame(
                -0.3e5,
                $metrics[$i]->value
            );
        }

        foreach (range(12, 15) as $i) {
            $this->assertSame(
                3.332e5,
                $metrics[$i]->value
            );
        }

        foreach (range(16, 17) as $i) {
            $this->assertSame(
                3.332e-5,
                $metrics[$i]->value
            );
        }

        foreach (range(18, 20) as $i) {
            $this->assertSame(
                -3.332e5,
                $metrics[$i]->value
            );
        }

        foreach (range(21, 22) as $i) {
            $this->assertSame(
                -3.332e-5,
                $metrics[$i]->value
            );
        }
    }

    function testInfinity(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_inf Tests for handling infinite values.
# TYPE test_inf summary
test_inf -inf
test_inf -Inf
test_inf -INF
test_inf -infinity
test_inf -Infinity
test_inf -INFINITY
test_inf +inf
test_inf +Inf
test_inf +INF
test_inf +infinity
test_inf +Infinity
test_inf +INFINITY
SCHEMA
        );

        $metrics = $node->getMetrics()['test_inf']->metrics;

        foreach (range(0, 5) as $i) {
            $this->assertLessThan(
                0,
                $metrics[$i]->value
            );
            $this->assertInfinite(
                $metrics[$i]->value
            );
        }

        foreach (range(6, 11) as $i) {
            $this->assertGreaterThan(
                0,
                $metrics[$i]->value
            );
            $this->assertInfinite(
                $metrics[$i]->value
            );
        }
    }

    function testNotANumber(): void
    {
        $node = $this->parser->parse(<<<'SCHEMA'
# HELP test_nan Tests for handling 'not a number' values.
# TYPE test_nan summary
test_nan nan
test_nan Nan
test_nan NaN
test_nan NAN
SCHEMA
        );

        $metrics = $node->getMetrics()['test_nan']->metrics;

        foreach (range(0, 3) as $i) {
            $this->assertNan(
                $metrics[$i]->value
            );
        }
    }
}
