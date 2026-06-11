<?php

declare(strict_types=1);

namespace Butschster\Prometheus\Validation;

use Butschster\Prometheus\Ast\SchemaNode;

interface ValidatorInterface
{
    /**
     * Validate the parsed schema.
     *
     * @throws \Butschster\Prometheus\Exceptions\ValidationException
     */
    public function validate(SchemaNode $schema): void;
}
