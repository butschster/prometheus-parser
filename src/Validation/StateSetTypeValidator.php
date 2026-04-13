<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Validation;

use Butschster\Prometheus\Ast\MetricDataNode;
use Butschster\Prometheus\Ast\MetricNode;
use Butschster\Prometheus\Ast\SchemaNode;
use Butschster\Prometheus\Exceptions\InvalidLabelValueException;
use Butschster\Prometheus\Exceptions\InvalidMetricValueException;

/**
 * Validates OpenMetrics `stateset` type metric families.
 *
 * Rules:
 *  - Each sample must have a label whose name equals the metric family name
 *    (this label identifies the state being reported).
 *  - The metric value must be 0 or 1 (false / true).
 */
final class StateSetTypeValidator implements ValidatorInterface
{
    public function validate(SchemaNode $schema): void
    {
        foreach ($schema->getMetrics() as $family) {
            if ($family->type !== 'stateset') {
                continue;
            }

            $this->validateFamily($family);
        }
    }

    private function validateFamily(MetricDataNode $family): void
    {
        foreach ($family->metrics as $metric) {
            $this->validateMetric($family->name, $metric);
        }
    }

    private function validateMetric(string $familyName, MetricNode $metric): void
    {
        $hasStateLabel = false;
        foreach ($metric->labels as $label) {
            if ($label->name === $familyName) {
                $hasStateLabel = true;
                break;
            }
        }

        if (!$hasStateLabel) {
            throw new InvalidLabelValueException(
                \sprintf(
                    'StateSet metric "%s" is missing required label "%s".',
                    $metric->name,
                    $familyName
                )
            );
        }

        if ($metric->value !== 0 && $metric->value !== 1 && $metric->value !== 0.0 && $metric->value !== 1.0) {
            throw new InvalidMetricValueException(
                \sprintf(
                    'StateSet metric "%s" value must be "0" or "1", got %s.',
                    $metric->name,
                    $metric->value
                )
            );
        }
    }
}

