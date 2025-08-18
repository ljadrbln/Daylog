<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries;

use Codeception\Test\Unit;
use Daylog\Application\UseCases\Entries\ListEntries;
use Daylog\Application\DTO\Entries\ListEntriesRequest;
use Daylog\Application\DTO\Entries\ListEntriesResponse;
use Daylog\Domain\Interfaces\EntryRepositoryInterface;

/**
 * Unit tests for UC-2 List Entries.
 *
 * These tests cover the behavior of listing entries:
 * - default pagination and sorting
 * - filtering by date range and exact date
 * - full-text query filter
 * - clamping of perPage parameter
 * - fallback for invalid sort
 * - validation error for invalid date format
 * - stable secondary order when sort keys are equal
 *
 * Data source: a fake in-memory repository implementing EntryRepositoryInterface.
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 */
final class ListEntriesTest extends Unit
{
    /**
     * Happy path: no filters, default sort, first page returned with metadata.
     *
     * @return void
     */
    public function testListEntriesNoFiltersDefaultSortFirstPageWithMeta(): void
    {
        $repo = EntryRepositoryInterface::class;
        $repo = $this->createMock($repo);

        $request = new ListEntriesRequest();
        $useCase = new ListEntries($repo);

        $response = $useCase->execute($request);

        $this->assertInstanceOf(ListEntriesResponse::class, $response);
        $this->assertSame(1, $response->getPage());
    }

    /**
     * Filters by an inclusive date range.
     *
     * @return void
     */
    public function testListEntriesDateRangeFiltersByDateInclusive(): void
    {
        $repo = EntryRepositoryInterface::class;
        $repo = $this->createMock($repo);

        $request = new ListEntriesRequest(dateFrom: '2025-01-01', dateTo: '2025-01-31');
        $useCase = new ListEntries($repo);

        $response = $useCase->execute($request);

        $this->assertInstanceOf(ListEntriesResponse::class, $response);
        // Expect all items in the returned list to fall within the date range
    }

    // TODO: add more Red tests:
    // - testListEntriesQuerySubstringMatchesTitleOrBodyCaseInsensitive
    // - testListEntriesPerPageOutOfBoundsIsClamped
    // - testListEntriesInvalidSortFallsBackToDefault
    // - testListEntriesInvalidDateFormatReturnsValidationError
    // - testListEntriesExactDateFiltersExactMatch
    // - testListEntriesStableSecondaryOrderByCreatedAtDesc
}
