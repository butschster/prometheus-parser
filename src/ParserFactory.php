<?php

declare(strict_types=1);

namespace Butschster\Prometheus;

use Butschster\Prometheus\Exceptions\GrammarFileNotFoundException;

final class ParserFactory
{
    /**
     * Create parser from grammar file
     */
    public static function create(): Parser
    {
        $path = __DIR__ . '/grammar.php';
        if (!file_exists($path)) {
            throw new GrammarFileNotFoundException(\sprintf("Grammar file %s not found." . $path));
        }

        /** @var array $data */
        $data = require $path;

        return new Parser($data);
    }
}
