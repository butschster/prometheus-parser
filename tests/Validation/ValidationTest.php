<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Validation;

use Butschster\Prometheus\Exceptions\InvalidLabelValueException;
use Butschster\Prometheus\Exceptions\InvalidMetricValueException;
use Butschster\Prometheus\Exceptions\InvalidUnitSuffixException;
use Butschster\Prometheus\Exceptions\ParseException;
use Butschster\Prometheus\Exceptions\UnexpectedTokenException;
use Butschster\Prometheus\ParserFactory;
use Butschster\Prometheus\Validation\InfoTypeValidator;
use Butschster\Prometheus\Validation\StateSetTypeValidator;
use Butschster\Prometheus\Validation\UnitSuffixValidator;

class ValidationTest extends \Butschster\Prometheus\Tests\TestCase
{
    // -------------------------------------------------------------------------
    //  InfoTypeValidator
    // -------------------------------------------------------------------------

    function testInfoValidNameAndValue(): void
    {
        $parser = ParserFactory::create();
        $parser->addValidator(new InfoTypeValidator());

        $schema = $parser->parse(<<<'SCHEMA'
# TYPE target_info info
target_info{version="1.0"} 1
SCHEMA
        );

        $this->assertNotNull($schema);
    }

    function testInfoInvalidNameMissingSuffix(): void
    {
        $this->expectException(InvalidMetricValueException::class);
        $this->expectExceptionMessageMatches('/_info/');

        $parser = ParserFactory::create();
        $parser->addValidator(new InfoTypeValidator());

        $parser->parse(<<<'SCHEMA'
# TYPE target info
target{version="1.0"} 1
SCHEMA
        );
    }

    function testInfoInvalidValueNotOne(): void
    {
        $this->expectException(InvalidMetricValueException::class);
        $this->expectExceptionMessageMatches('/value of 1/');

        $parser = ParserFactory::create();
        $parser->addValidator(new InfoTypeValidator());

        $parser->parse(<<<'SCHEMA'
# TYPE target_info info
target_info{version="1.0"} 2
SCHEMA
        );
    }

    function testInfoNonInfoTypeSkipped(): void
    {
        $parser = ParserFactory::create();
        $parser->addValidator(new InfoTypeValidator());

        // gauge type with value 2 — validator should not complain
        $schema = $parser->parse(<<<'SCHEMA'
# TYPE target gauge
target 2
SCHEMA
        );

        $this->assertNotNull($schema);
    }

    // -------------------------------------------------------------------------
    //  StateSetTypeValidator
    // -------------------------------------------------------------------------

    function testStateSetValidValues(): void
    {
        $parser = ParserFactory::create();
        $parser->addValidator(new StateSetTypeValidator());

        $schema = $parser->parse(<<<'SCHEMA'
# TYPE my_state stateset
my_state{my_state="foo"} 1
my_state{my_state="bar"} 0
SCHEMA
        );

        $this->assertNotNull($schema);
    }

    function testStateSetInvalidMetricValueNotZeroOrOne(): void
    {
        $this->expectException(InvalidMetricValueException::class);
        $this->expectExceptionMessageMatches('/"0" or "1"/');

        $parser = ParserFactory::create();
        $parser->addValidator(new StateSetTypeValidator());

        $parser->parse(<<<'SCHEMA'
# TYPE my_state stateset
my_state{my_state="foo"} 1
my_state{my_state="bar"} 0
my_state{my_state="baz"} 2
SCHEMA
        );
    }

    function testStateSetMissingRequiredLabel(): void
    {
        $this->expectException(InvalidLabelValueException::class);
        $this->expectExceptionMessageMatches('/missing required label/');

        $parser = ParserFactory::create();
        $parser->addValidator(new StateSetTypeValidator());

        $parser->parse(<<<'SCHEMA'
# TYPE my_state stateset
my_state{other_label="foo"} 1
SCHEMA
        );
    }

    // -------------------------------------------------------------------------
    //  UnitSuffixValidator
    // -------------------------------------------------------------------------

    function testUnitSuffixValid(): void
    {
        $parser = ParserFactory::create();
        $parser->addValidator(new UnitSuffixValidator());

        $schema = $parser->parse(<<<'SCHEMA'
# TYPE http_request_duration_seconds gauge
# UNIT http_request_duration_seconds seconds
http_request_duration_seconds 1.5
SCHEMA
        );

        $this->assertNotNull($schema);
    }

    function testUnitSuffixInvalid(): void
    {
        $this->expectException(InvalidUnitSuffixException::class);
        $this->expectExceptionMessageMatches('/_seconds/');

        $parser = ParserFactory::create();
        $parser->addValidator(new UnitSuffixValidator());

        $parser->parse(<<<'SCHEMA'
# TYPE http_request_duration gauge
# UNIT http_request_duration seconds
http_request_duration 1.5
SCHEMA
        );
    }

    function testUnitSuffixNullUnitSkipped(): void
    {
        $parser = ParserFactory::create();
        $parser->addValidator(new UnitSuffixValidator());

        // No UNIT directive — validator skips
        $schema = $parser->parse(<<<'SCHEMA'
# TYPE http_request_duration gauge
http_request_duration 1.5
SCHEMA
        );

        $this->assertNotNull($schema);
    }

    // -------------------------------------------------------------------------
    //  Multiple validators chained
    // -------------------------------------------------------------------------

    function testMultipleValidatorsChained(): void
    {
        $parser = ParserFactory::create();
        $parser->addValidator(new UnitSuffixValidator());
        $parser->addValidator(new InfoTypeValidator());

        $schema = $parser->parse(<<<'SCHEMA'
# TYPE http_request_duration_seconds gauge
# UNIT http_request_duration_seconds seconds
http_request_duration_seconds 1.5
# TYPE target_info info
target_info{version="1"} 1
SCHEMA
        );

        $this->assertNotNull($schema);
    }

    // -------------------------------------------------------------------------
    //  Structured exceptions (ParseException / UnexpectedTokenException)
    // -------------------------------------------------------------------------

    function testParseExceptionOnSyntaxError(): void
    {
        $this->expectException(ParseException::class);

        $parser = ParserFactory::create();
        // Invalid syntax: label without closing brace
        $parser->parse('invalid_metric{broken 1');
    }

    function testUnexpectedTokenExceptionIsParseException(): void
    {
        $parser = ParserFactory::create();

        try {
            $parser->parse('!!!invalid');
            $this->fail('Expected ParseException');
        } catch (ParseException $e) {
            $this->assertInstanceOf(UnexpectedTokenException::class, $e);
        }
    }
}
