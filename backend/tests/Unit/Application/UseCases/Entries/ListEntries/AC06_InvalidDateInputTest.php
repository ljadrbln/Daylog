<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\DataProviders\ListEntriesDateDataProvider;
use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;
use Daylog\Tests\Support\Assertion\EntryValidationAssertions;

/**
 * UC-2 / AC-6 — Invalid date input — Unit.
 *
 * Purpose:
 * Ensure that invalid date inputs are rejected by validation and the repository is not touched.
 *
 * Mechanics:
 * - Build a request where exactly one of date/dateFrom/dateTo is invalid;
 * - Configure validator mock to throw DomainValidationException('DATE_INVALID');
 * - Execute and assert exception; repo remains untouched.
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries\ListEntries::execute
 * @group UC-ListEntries
 */
final class AC06_InvalidDateInputTest extends BaseListEntriesUnitTest
{
    use EntryValidationAssertions;
    use ListEntriesDateDataProvider;

    /**
     * Invalid date inputs must trigger DATE_INVALID and avoid repository access.
     *
     * @dataProvider provideInvalidDateInputs
     *
     * @param string $field
     * @param string $value
     * @return void
     */
    public function testInvalidDateInputTriggersValidationError(string $field, string $value): void
    {
        // Arrange
        $request   = ListEntriesTestRequestFactory::withDate($field, $value);
        $validator = $this->makeValidatorThrows('DATE_INVALID');
        $repo      = $this->makeRepo();

        // Expect
        $this->expectDateInvalid();

        // Act
        $useCase = $this->makeUseCase($repo, $validator);
        $useCase->execute($request);
    }
}
