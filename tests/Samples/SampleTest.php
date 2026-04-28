<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Tests\Samples;

use Butschster\Prometheus\ParserFactory;

/**
 * Test that samples collected from various sources can be parsed.
 *
 * @coversNothing
 */
class SampleTest extends \Butschster\Prometheus\Tests\TestCase
{
    /**
     * @dataProvider fileProvider
     * @doesNotPerformAssertions
     */
    public function testSample(string $file): void
    {
        $parser = ParserFactory::create();
        $parser->parse(\file_get_contents($file));
    }

    public function fileProvider(): \Iterator
    {
        $fs = new \FilesystemIterator(
            __DIR__ . '/Files',
            \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::CURRENT_AS_PATHNAME
        );

        foreach ($fs as $fileName => $pathName) {
            yield $fileName => [$pathName];
        }
    }
}
