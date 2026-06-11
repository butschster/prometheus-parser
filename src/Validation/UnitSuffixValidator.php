<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Validation;

use Butschster\Prometheus\Ast\SchemaNode;
use Butschster\Prometheus\Exceptions\InvalidUnitSuffixException;

/**
 * Validates that metric family names end with the declared unit suffix.
 *
 * OpenMetrics requires: if `# UNIT foo_seconds seconds` is declared,
 * the metric family name must end with `_seconds`.
 */
final class UnitSuffixValidator implements ValidatorInterface
{
    public function validate(SchemaNode $schema): void
    {
        foreach ($schema->getMetrics() as $family) {
            if ($family->unit === null || $family->unit === '') {
                continue;
            }

            $expectedSuffix = '_' . $family->unit;

            if (!\str_ends_with($family->name, $expectedSuffix)) {
                throw new InvalidUnitSuffixException(
                    \sprintf(
                        'Metric family "%s" has unit "%s" but the name does not end with "_%s".',
                        $family->name,
                        $family->unit,
                        $family->unit
                    )
                );
            }
        }
    }
}
