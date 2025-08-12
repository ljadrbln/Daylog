<?php
declare(strict_types=1);

namespace Daylog\Domain\Errors;

/**
 * Domain-level validation error.
 * Use this for invariant and input rule violations in the domain layer.
 */
class ValidationException extends \RuntimeException
{
}

