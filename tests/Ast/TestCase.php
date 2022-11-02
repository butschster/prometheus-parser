<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Ast;

use Butschster\Prometheus\Parser;
use Butschster\Prometheus\ParserFactory;

abstract class TestCase extends \Butschster\Prometheus\Tests\Parsers\TestCase
{
    protected Parser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = ParserFactory::create();
    }
}
