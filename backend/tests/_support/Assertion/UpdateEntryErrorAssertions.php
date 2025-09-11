<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Assertion;

use Daylog\Application\Exceptions\DomainValidationException;

/**
 * Cross-suite assertions for UC-5 errors.
 *
 * Purpose:
 * Unify exception expectations for AC-5..AC-9..AC-14.
 */
trait UpdateEntryErrorAssertions
{
    /**
     * Expect ENTRY_NOT_FOUND.
     *
     * @return void
     */
    protected function expectEntryNotFound(): void
    {
        $class   = DomainValidationException::class;
        $message = 'ENTRY_NOT_FOUND';

        $this->expectException($class);
        $this->expectExceptionMessage($message);
    }
}
