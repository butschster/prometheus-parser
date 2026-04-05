<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests;

use Phplrt\Compiler\Compiler;
use Phplrt\Source\File;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected const GRAMMAR_FILE_PATH = '/../src/grammar.php';
    protected const EBNF_FILE_PATH = '/../ebnf.pp2';

    protected static Compiler $compiler;

    public static function setUpBeforeClass(): void
    {
        if (isset(self::$compiler)) {
            return;
        }

        self::$compiler = new Compiler();
        self::$compiler->load(File::fromPathname(__DIR__ . self::EBNF_FILE_PATH));

        \file_put_contents(
            __DIR__ . self::GRAMMAR_FILE_PATH,
            (string)self::$compiler->build()
        );
    }
}
