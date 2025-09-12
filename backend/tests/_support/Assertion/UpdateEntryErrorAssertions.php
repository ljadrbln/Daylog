<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Assertion;

use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Tests\Support\Fakes\FakeEntryRepository;

/**
 * Assertions for UpdateEntry error flows.
 *
 * Purpose:
 * Provide unified expectations for common error codes.
 */
trait UpdateEntryErrorAssertions
{
    /**
     * Assert that repository has not been touched (no save calls).
     *
     * @param FakeEntryRepository $repo FakeEntryRepository with getSaveCalls() method.
     * @return void
     */
    protected function assertRepoUntouched(FakeEntryRepository $repo): void
    {
        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);
    }

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

    /**
     * Expect ID_INVALID validation error.
     *
     * @return void
     */
    protected function expectIdInvalid(): void
    {
        $class   = DomainValidationException::class;
        $message = 'ID_INVALID';

        $this->expectException($class);
        $this->expectExceptionMessage($message);
    }

    /**
     * Expect ID_INVALID validation error.
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

    /**
     * Expect NO_FIELDS_TO_UPDATE validation error.
     *
     * @return void
     */
    protected function expectNoFieldsToUpdate(): void
    {
        $class   = DomainValidationException::class;
        $message = 'NO_FIELDS_TO_UPDATE';

        $this->expectException($class);
        $this->expectExceptionMessage($message);
    }

    /**
     * Expect TITLE_REQUIRED validation error.
     *
     * @return void
     */
    protected function expectTitleRequired(): void
    {
        $class   = DomainValidationException::class;
        $message = 'TITLE_REQUIRED';

        $this->expectException($class);
        $this->expectExceptionMessage($message);
    }
    
    /**
     * Expect TITLE_TOO_LONG validation error.
     *
     * @return void
     */
    protected function expectTitleTooLong(): void
    {
        $class   = DomainValidationException::class;
        $message = 'TITLE_TOO_LONG';

        $this->expectException($class);
        $this->expectExceptionMessage($message);
    }

    /**
     * Expect BODY_REQUIRED validation error.
     *
     * @return void
     */
    protected function expectBodyRequired(): void
    {
        $class   = DomainValidationException::class;
        $message = 'BODY_REQUIRED';

        $this->expectException($class);
        $this->expectExceptionMessage($message);
    }

    /**
     * Expect BODY_TOO_LONG validation error.
     *
     * @return void
     */
    protected function expectBodyTooLong(): void
    {
        $class   = DomainValidationException::class;
        $message = 'BODY_TOO_LONG';

        $this->expectException($class);
        $this->expectExceptionMessage($message);
    }

    /**
     * Expect DATE_INVALID validation error.
     *
     * @return void
     */
    protected function expectDateInvalid(): void
    {
        $class   = DomainValidationException::class;
        $message = 'DATE_INVALID';

        $this->expectException($class);
        $this->expectExceptionMessage($message);
    }

    /**
     * Expect NO_CHANGES_APPLIED validation error.
     *
     * @return void
     */
    protected function expectNoChangesApplied(): void
    {
        $class   = DomainValidationException::class;
        $message = 'NO_CHANGES_APPLIED';

        $this->expectException($class);
        $this->expectExceptionMessage($message);
    }    
}
