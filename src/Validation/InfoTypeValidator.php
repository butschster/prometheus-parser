<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Validation;

use Butschster\Prometheus\Ast\MetricDataNode;
use Butschster\Prometheus\Ast\SchemaNode;
use Butschster\Prometheus\Exceptions\InvalidMetricValueException;

/**
 * Validates OpenMetrics `info` type metric families.
 *
 * Rules:
 *  - The metric family name must end with `_info`.
 *  - Every sample value must be exactly `1`.
 */
final class InfoTypeValidator implements ValidatorInterface
{
    public function validate(SchemaNode $schema): void
    {
        foreach ($schema->getMetrics() as $family) {
            if ($family->type !== 'info') {
                continue;
            }

            $this->validateFamily($family);
        }
    }

    private function validateFamily(MetricDataNode $family): void
    {
        if (!\str_ends_with($family->name, '_info')) {
            throw new InvalidMetricValueException(
                \sprintf(
                    'Info metric family "%s" must have a name ending with "_info".',
                    $family->name
                )
            );
        }

        foreach ($family->metrics as $metric) {
            if ($metric->value !== 1 && $metric->value !== 1.0) {
                throw new InvalidMetricValueException(
                    \sprintf(
                        'Info metric "%s" must have a value of 1, got %s.',
                        $metric->name,
                        $metric->value
                    )
                );
            }
        }
    }
}
