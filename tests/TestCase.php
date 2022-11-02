<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected const GRAMMAR_FILE_PATH = '/../../src/grammar.php';
    protected const EBNF_FILE_PATH = '/../../ebnf.pp2';
}
