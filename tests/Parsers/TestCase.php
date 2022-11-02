<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Parsers;

use Phplrt\Compiler\Compiler;
use Phplrt\Source\File;

abstract class TestCase extends \Butschster\Prometheus\Tests\TestCase
{
    protected readonly Compiler $compiler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->compiler = new Compiler();
        $this->compiler->load(File::fromPathname(__DIR__ . static::EBNF_FILE_PATH));

        \file_put_contents(
            __DIR__ . static::GRAMMAR_FILE_PATH,
            (string)$this->compiler->build()
        );
    }

    public function assertAst(string $schema, string $ast)
    {
        $ast = \array_map(function (string $line) {
            if (empty($line)) {
                return $line;
            }
            return $line;
        }, \explode("\n", $ast));

        $ast = \implode("\n", $ast);

        $this->assertEquals($ast,
            (string )$this->compiler->parse($schema)
        );
    }
}
