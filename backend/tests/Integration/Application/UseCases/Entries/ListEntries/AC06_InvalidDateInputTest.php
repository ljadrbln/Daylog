<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Assertion\EntryValidationAssertions;
use Daylog\Tests\Support\DataProviders\ListEntriesDateDataProvider;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * UC-2 / AC-06 — Invalid date input — Integration.
 *
 * Purpose:
 * Ensure invalid values in date/dateFrom/dateTo trigger DomainValidationException with DATE_INVALID.
 *
 * Mechanics:
 * - Build baseline request via the test factory;
 * - override one date field per dataset from a shared DataProvider;
 * - execute and assert that DATE_INVALID is raised by the use case.
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 *
 * @group UC-ListEntries
 */
final class AC06_InvalidDateInputTest extends BaseListEntriesIntegrationTest
{
    use EntryValidationAssertions;
    use ListEntriesDateDataProvider;

    /**
     * AC-06: Invalid date input yields DATE_INVALID.
     *
     * @dataProvider provideInvalidDateInputs
     *
     * @param string $field One of: date|dateFrom|dateTo.
     * @param string $value Raw invalid date string.
     * @return void
     */
    public function testInvalidDateInputThrowsValidationException(string $field, string $value): void
    {
        // Arrange
        $dataset = ListEntriesDataset::ac06InvalidDateInput($field, $value);

        // Expectation
        $this->expectDateInvalid();

        // Act
        $request = $dataset['request'];
        $this->useCase->execute($request);
    }
}