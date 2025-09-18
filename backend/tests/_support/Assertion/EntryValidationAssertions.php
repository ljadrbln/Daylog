<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Assertion;

use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Exceptions\NotFoundException;
use Daylog\Tests\Support\Fakes\FakeEntryRepository;

/**
 * Unified assertions for Entry-related error flows (UC-1 AddEntry, UC-5 UpdateEntry).
 *
 * Purpose:
 * Provide common helpers for expecting specific DomainValidationException codes
 * and asserting that repositories were not touched.
 *
 * Mechanics:
 * - Call $this->expect<ErrorCode>() before executing the use case under test.
 * - Call $this->assertRepoUntouched($repo) afterwards to verify no persistence occurred.
 */
trait EntryValidationAssertions
{
    /**
     * Assert that repository has not been touched (no save calls).
     *
     * @param FakeEntryRepository $repo Fake repository.
     * @return void
     */
    protected function assertRepoUntouched(FakeEntryRepository $repo): void
    {
        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);
    }

    /**
     * Generic expectation for DomainValidationException with a given code.
     *
     * @param string $code Expected error code.
     * @return void
     */
    private function expectDomainError(string $code): void
    {
        $class = DomainValidationException::class;
        $this->expectException($class);
        $this->expectExceptionMessage($code);
    }

    /**
     * Expect ID_REQUIRED domain error.
     *
     * @return void
     */
    protected function expectIdRequired(): void
    {
        $this->expectDomainError('ID_REQUIRED');
    }

    /**
     * Expect ID_INVALID domain error.
     *
     * @return void
     */
    protected function expectIdInvalid(): void
    {
        $this->expectDomainError('ID_INVALID');
    }

    /**
     * Expect ENTRY_NOT_FOUND (NotFoundException).
     *
     * @return void
     */
    protected function expectEntryNotFound(): void
    {
        $class = NotFoundException::class;
        $this->expectException($class);
        $this->expectExceptionMessage('ENTRY_NOT_FOUND');
    }

    /**
     * Expect NO_FIELDS_TO_UPDATE domain error.
     *
     * @return void
     */
    protected function expectNoFieldsToUpdate(): void
    {
        $this->expectDomainError('NO_FIELDS_TO_UPDATE');
    }

    /**
     * Expect NO_CHANGES_APPLIED informational code.
     *
     * @return void
     */
    protected function expectNoChangesApplied(): void
    {
        $this->expectDomainError('NO_CHANGES_APPLIED');
    }

    /**
     * Expect TITLE_REQUIRED domain error.
     *
     * @return void
     */
    protected function expectTitleRequired(): void
    {
        $this->expectDomainError('TITLE_REQUIRED');
    }

    /**
     * Expect TITLE_TOO_LONG domain error.
     *
     * @return void
     */
    protected function expectTitleTooLong(): void
    {
        $this->expectDomainError('TITLE_TOO_LONG');
    }

    /**
     * Expect BODY_REQUIRED domain error.
     *
     * @return void
     */
    protected function expectBodyRequired(): void
    {
        $this->expectDomainError('BODY_REQUIRED');
    }

    /**
     * Expect BODY_TOO_LONG domain error.
     *
     * @return void
     */
    protected function expectBodyTooLong(): void
    {
        $this->expectDomainError('BODY_TOO_LONG');
    }

    /**
     * Expect DATE_REQUIRED domain error.
     *
     * @return void
     */
    protected function expectDateRequired(): void
    {
        $this->expectDomainError('DATE_REQUIRED');
    }

    /**
     * Expect DATE_INVALID domain error.
     *
     * @return void
     */
    protected function expectDateInvalid(): void
    {
        $this->expectDomainError('DATE_INVALID');
    }

    /**
     * Expect QUERY_TOO_LONG domain error.
     *
     * @return void
     */
    protected function expectQueryTooLong(): void
    {
        $this->expectDomainError('QUERY_TOO_LONG');
    }

    /**
     * Expect DATE_RANGE_INVALID domain error.
     *
     * @return void
     */
    protected function expectDateRangeInvalid(): void
    {
        $this->expectDomainError('DATE_RANGE_INVALID');
    }
}
