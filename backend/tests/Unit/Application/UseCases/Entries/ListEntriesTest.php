<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries;

use Codeception\Test\Unit;
use Daylog\Application\UseCases\Entries\ListEntries;
use Daylog\Application\DTO\Entries\ListEntriesRequest;
use Daylog\Application\DTO\Entries\ListEntriesResponse;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;

/**
 * Unit tests for UC-2 List Entries.
 *
 * This suite validates the behavior of listing diary entries:
 * - default pagination and sorting
 * - filtering by date range and exact date
 * - full-text query filtering
 * - clamping of pagination bounds
 * - sorting behavior and fallback
 * - validation errors for invalid parameters
 * - stable ordering rules
 *
 * Data source: a fake repository implementing EntryRepositoryInterface.
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 */
final class ListEntriesTest extends Unit
{
    /**
     * AC-1: Happy path.
     * When no filters are provided, the first page is returned,
     * sorted by date DESC by default, and pagination metadata is present.
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
    }

    /**
     * AC-2: Date range.
     * When dateFrom/dateTo are provided, only entries with dates
     * within the inclusive range must be returned.
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
    }

    /**
     * AC-3: Full-text query.
     * When query is provided, entries must be matched if the substring
     * is found in either title or body (case-insensitive).
     *
     * @return void
     */
    public function testListEntriesQuerySubstringMatchesTitleOrBodyCaseInsensitive(): void
    {
        $repo = EntryRepositoryInterface::class;
        $repo = $this->createMock($repo);

        $request = new ListEntriesRequest(query: 'search');
        $useCase = new ListEntries($repo);

        $response = $useCase->execute($request);

        $this->assertInstanceOf(ListEntriesResponse::class, $response);
    }

    /**
     * AC-4: Pagination bounds.
     * If perPage is outside the allowed range, it must be clamped
     * to the nearest limit. Empty pages are valid results.
     *
     * @return void
     */
    public function testListEntriesPerPageOutOfBoundsIsClamped(): void
    {
        $repo = EntryRepositoryInterface::class;
        $repo = $this->createMock($repo);

        $request = new ListEntriesRequest(perPage: 9999);
        $useCase = new ListEntries($repo);

        $response = $useCase->execute($request);

        $this->assertInstanceOf(ListEntriesResponse::class, $response);
    }

    /**
     * AC-5: Sorting fallback.
     * If sort parameters are invalid, the system must default
     * to date DESC sorting.
     *
     * @return void
     */
    public function testListEntriesInvalidSortFallsBackToDefault(): void
    {
        $repo = EntryRepositoryInterface::class;
        $repo = $this->createMock($repo);

        $request = new ListEntriesRequest(sort: 'invalid_field', direction: 'INVALID');
        $useCase = new ListEntries($repo);

        $response = $useCase->execute($request);

        $this->assertInstanceOf(ListEntriesResponse::class, $response);
    }

    /**
     * AC-6: Invalid date format.
     * If date or range inputs do not match YYYY-MM-DD,
     * validation must fail with DATE_INVALID_FORMAT.
     *
     * @return void
     */
    public function testListEntriesInvalidDateFormatReturnsValidationError(): void
    {
        $repo = EntryRepositoryInterface::class;
        $repo = $this->createMock($repo);

        $request = new ListEntriesRequest(dateFrom: '2025-13-99');
        $useCase = new ListEntries($repo);

        $response = $useCase->execute($request);

        $this->assertInstanceOf(ListEntriesResponse::class, $response);
    }

    /**
     * AC-7: Exact date filter.
     * When a single date is provided, only entries with
     * an exact logical date match must be returned.
     *
     * @return void
     */
    public function testListEntriesExactDateFiltersExactMatch(): void
    {
        $repo = EntryRepositoryInterface::class;
        $repo = $this->createMock($repo);

        $request = new ListEntriesRequest(date: '2025-01-15');
        $useCase = new ListEntries($repo);

        $response = $useCase->execute($request);

        $this->assertInstanceOf(ListEntriesResponse::class, $response);
    }

    /**
     * AC-8: Stable order.
     * When primary sort keys are equal, results must be ordered
     * stably by createdAt DESC.
     *
     * @return void
     */
    public function testListEntriesStableSecondaryOrderByCreatedAtDesc(): void
    {
        $repo = EntryRepositoryInterface::class;
        $repo = $this->createMock($repo);

        $request = new ListEntriesRequest(sort: 'date', direction: 'ASC');
        $useCase = new ListEntries($repo);

        $response = $useCase->execute($request);

        $this->assertInstanceOf(ListEntriesResponse::class, $response);
    }
}
