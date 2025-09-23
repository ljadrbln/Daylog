<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\DataProviders\ListEntriesDateDataProvider;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-6 — Invalid date input — Unit.
 *
 * Purpose:
 * Ensure that invalid date inputs are rejected by validation and the repository is not touched.
 *
 * Mechanics:
 * - Build a request via dataset where exactly one of date/dateFrom/dateTo is invalid;
 * - Configure validator mock to throw DomainValidationException('DATE_INVALID');
 * - Execute and assert exception; repository must not be called.
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
     * @param string $field One of: date|dateFrom|dateTo.
     * @param string $value Invalid raw value to inject.
     * @return void
     */
    public function testInvalidDateInputTriggersValidationError(string $field, string $value): void
    {
        // Arrange
        $dataset = ListEntriesDataset::ac06InvalidDateInput($field, $value);

        $repo      = $this->makeRepo();
        $validator = $this->makeValidatorThrows('DATE_INVALID');
        $useCase   = $this->makeUseCase($repo, $validator);

        // Expect
        $this->expectDateInvalid();

        // Act
        $request = $dataset['request'];
        $useCase->execute($request);
    }
}
