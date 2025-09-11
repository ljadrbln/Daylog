<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Assertion;

use Daylog\Application\Exceptions\DomainValidationException;

/**
 * Assertions for UpdateEntry error flows.
 *
 * Purpose:
 * Provide unified expectations for common error codes.
 */
trait UpdateEntryErrorAssertions
{
    /**
     * Expect ID_REQUIRED validation error.
     *
     * @return void
     */
    protected function expectIdRequired(): void
    {
        $class   = DomainValidationException::class;
        $message = 'ID_REQUIRED';

        $this->expectException($class);
        $this->expectExceptionMessage($message);
    }
}
