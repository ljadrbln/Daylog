<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries;

use Codeception\Test\Unit;
use Daylog\Application\UseCases\Entries\ListEntries;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Application\Validators\Entries\ListEntries\ListEntriesValidatorInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntryHelper;
use Daylog\Tests\Support\Helper\EntryRowHelper;
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
        $entries = [
            Entry::fromArray(EntryHelper::getData('Oldest', 'Valid body', '2025-08-10')),
            Entry::fromArray(EntryHelper::getData('Newest', 'Valid body', '2025-08-12')),
            Entry::fromArray(EntryHelper::getData('Middle', 'Valid body', '2025-08-11'))
        ];        

        $ids = [
            '00000000-0000-0000-0000-000000000001',
            '00000000-0000-0000-0000-000000000002',
            '00000000-0000-0000-0000-000000000003',
        ];

        $timestamps = [
            '2025-08-10 10:00:00',
            '2025-08-12 10:00:00',
            '2025-08-11 10:00:00',
        ];
        
        $rows = EntryRowHelper::makeRows($ids, $entries, $timestamps);

        $page     = 1;
        $perPage  = 10;
        $total    = count($entries);
        $pagesCnt = (int)ceil($total / $perPage);

        $pageResult = [
            'items'      => $rows,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'pagesCount' => $pagesCnt,
        ];

        $repoClass = EntryRepositoryInterface::class;
        $repo      = $this->createMock($repoClass);

        $repo
            ->expects($this->once())
            ->method('findByCriteria')
            ->willReturn($pageResult);

        $validatorClass = ListEntriesValidatorInterface::class;
        $validator      = $this->createMock($validatorClass);
        $validator
            ->expects($this->once())
            ->method('validate');

        $useCase = new ListEntries($repo, $validator);

        $data    = ListEntriesHelper::getData();
        $request = ListEntriesHelper::buildRequest($data);

        /** Act **/
        $response = $useCase->execute($request);

        /** Assert **/
        $items = $response->getItems();
        $this->assertSame($entries[0]->getDate(), $items[0]->getDate());
        $this->assertSame($entries[1]->getDate(), $items[1]->getDate());
        $this->assertSame($entries[2]->getDate(), $items[2]->getDate());

        $this->assertSame(count($entries),        $response->getTotal());
        $this->assertSame($request->getPage(),    $response->getPage());
        $this->assertSame($request->getPerPage(), $response->getPerPage());
        $this->assertSame(1,                      $response->getPagesCount());
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
        $entries = [
            Entry::fromArray(EntryHelper::getData('D-09', 'Valid body', '2025-08-09')),
            Entry::fromArray(EntryHelper::getData('D-10', 'Valid body', '2025-08-10')),
            Entry::fromArray(EntryHelper::getData('D-11', 'Valid body', '2025-08-11')),
            Entry::fromArray(EntryHelper::getData('D-12', 'Valid body', '2025-08-12')),
        ];

        $ids = [
            '00000000-0000-0000-0000-000000000001',
            '00000000-0000-0000-0000-000000000002',
            '00000000-0000-0000-0000-000000000003',
            '00000000-0000-0000-0000-000000000004',
        ];

        $timestamps = [
            '2025-08-09 10:00:00',
            '2025-08-10 10:00:00',
            '2025-08-11 10:00:00',
            '2025-08-12 10:00:00',
        ];

        $rows = EntryRowHelper::makeRows($ids, $entries, $timestamps);

        $pageResult = [
            'items'      => $rows,
            'total'      => count($rows),
            'page'       => 1,
            'perPage'    => 10,
            'pagesCount' => 1,
        ];

        $repoClass = EntryRepositoryInterface::class;
        $repo      = $this->createMock($repoClass);
        $repo
            ->expects($this->once())
            ->method('findByCriteria')
            ->willReturn($pageResult);

        $validatorClass = ListEntriesValidatorInterface::class;
        $validator      = $this->createMock($validatorClass);
        $validator
            ->expects($this->once())
            ->method('validate');

        $useCase = new ListEntries($repo, $validator);

        $data    = ListEntriesHelper::getData();
        $request = ListEntriesHelper::buildRequest($data);

        /** Act **/
        $response = $useCase->execute($request);

        /** Assert **/
        $items = $response->getItems();

        $this->assertCount(4, $items);
        $this->assertSame($entries[0]->getDate(), $items[0]->getDate());
        $this->assertSame($entries[1]->getDate(), $items[1]->getDate());
        $this->assertSame($entries[2]->getDate(), $items[2]->getDate());
        $this->assertSame($entries[3]->getDate(), $items[3]->getDate());

        $this->assertSame(count($entries),          $response->getTotal());
        $this->assertSame($request->getPage(),      $response->getPage());
        $this->assertSame($request->getPerPage(),   $response->getPerPage());
        $this->assertSame(1,                        $response->getPagesCount());
    }
}
