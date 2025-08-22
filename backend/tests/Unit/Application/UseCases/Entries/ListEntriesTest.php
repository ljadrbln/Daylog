<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries;

use Codeception\Test\Unit;
use Daylog\Application\UseCases\Entries\ListEntries;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntryHelper;
use Daylog\Tests\Support\Helper\ListEntriesHelper;

/**
 * Unit tests for UC-2 ListEntries.
 *
 * This suite verifies:
 * 1) Scenario 1: default sort (happy path) pagination: default sort by date DESC with pagination metadata.
 * 2) Scenario 2: inclusive date range: inclusive filtering by dateFrom/dateTo with date DESC order.
 *
 * Notes:
 * - Storage is responsible for timestamps; domain Entry exposes logical date only.
 * - Repository is mocked; data source is synthesized via EntryHelper.
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 */
final class ListEntriesTest extends Unit
{
    /**
     * Ensures that ListEntries returns entries sorted by date DESC
     * when no filters are provided (happy path).
     *
     * @return void
     */
    public function testHappyPathReturnsEntriesSortedByDateDesc(): void
    {
        /** Arrange **/
        $repoClass = EntryRepositoryInterface::class;
        $repo      = $this->createMock($repoClass);

        $entry1 = EntryHelper::getData('Oldest', 'Valid body', '2025-08-10');
        $entry1 = Entry::fromArray($entry1);

        $entry2 = EntryHelper::getData('Newest', 'Valid body', '2025-08-12');
        $entry2 = Entry::fromArray($entry2);

        $entry3 = EntryHelper::getData('Middle', 'Valid body', '2025-08-11');
        $entry3 = Entry::fromArray($entry3);

        $entries = [$entry1, $entry2, $entry3];

        $repo
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn($entries);

        $data    = ListEntriesHelper::getData();
        $request = ListEntriesHelper::buildRequest($data);

        $useCase = new ListEntries($repo);

        /** Act **/
        $response = $useCase->execute($request);

        /** Assert **/
        $items = $response->getItems();
        $this->assertSame('2025-08-12', $items[0]->getDate());
        $this->assertSame('2025-08-11', $items[1]->getDate());
        $this->assertSame('2025-08-10', $items[2]->getDate());

        $this->assertSame(3, $response->getTotal());
        $this->assertSame(1, $response->getPage());
        $this->assertSame(10, $response->getPerPage()); // default perPage
    }

    /**
     * Scenario: inclusive date range filtering (see UC-2 ListEntries — date range):
     * given dateFrom/dateTo, only entries with logical date within [from..to] are returned,
     * ordered by date DESC; pagination metadata remains consistent.
     *
     * Data source:
     * - Four synthetic entries with dates 2025-08-09..12 produced via EntryHelper and Entry::fromArray().
     *
     * Checks:
     * - Items include only 2025-08-12, 2025-08-11, 2025-08-10 in that order.
     * - total/page/perPage/pagesCount reflect the filtered set.
     *
     * @return void
     */
    public function testDateRangeInclusiveReturnsMatchingItems(): void
    {
        /** Arrange **/
        $repoClass = EntryRepositoryInterface::class;
        $repo      = $this->createMock($repoClass);

        $entry1 = EntryHelper::getData('D-09', 'Valid body', '2025-08-09');
        $entry1 = Entry::fromArray($entry1);

        $entry2 = EntryHelper::getData('D-10', 'Valid body', '2025-08-10');
        $entry2 = Entry::fromArray($entry2);

        $entry3 = EntryHelper::getData('D-11', 'Valid body', '2025-08-11');
        $entry3 = Entry::fromArray($entry3);

        $entry4 = EntryHelper::getData('D-12', 'Valid body', '2025-08-12');
        $entry4 = Entry::fromArray($entry4);

        $entries = [$entry1, $entry2, $entry3, $entry4];

        $repo
            ->expects($this->once())
            ->method('fetchAll')
            ->willReturn($entries);

        $useCase = new ListEntries($repo);

        $page     = 1;
        $perPage  = 10;
        $dateFrom = '2025-08-10';
        $dateTo   = '2025-08-12';

        /** @var array<string,mixed> $params */
        $params = [
            'page'     => $page,
            'perPage'  => $perPage,
            'dateFrom' => $dateFrom,
            'dateTo'   => $dateTo,
        ];

        $data    = ListEntriesHelper::getData();
        $request = ListEntriesHelper::buildRequest($data);

        /** Act **/
        $response = $useCase->execute($request);

        /** Assert **/
        $items = $response->getItems();

        $this->assertCount(3, $items);
        $this->assertSame('2025-08-12', $items[0]->getDate());
        $this->assertSame('2025-08-11', $items[1]->getDate());
        $this->assertSame('2025-08-10', $items[2]->getDate());

        $total     = $response->getTotal();
        $page      = $response->getPage();
        $perPage   = $response->getPerPage();
        $pagesCnt  = $response->getPagesCount();

        $this->assertSame(3, $total);
        $this->assertSame(1, $page);
        $this->assertSame(10, $perPage);
        $this->assertSame(1, $pagesCnt);
    }
}
