<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries;

use Codeception\Test\Unit;
use Daylog\Application\UseCases\Entries\ListEntries;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Application\Validators\Entries\ListEntries\ListEntriesValidatorInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntryTestData;
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
 * - Repository is mocked; data source is synthesized via EntryTestData.
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 * @group UC-ListEntries
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
        // Arrange
        $total = 3;
        $step  = 1;
        $rows = EntryTestData::getMany($total, $step);
        
        $entries = [];
        foreach ($rows as $row) {
            $entries[] = Entry::fromArray($row);
        }

        $page     = 1;
        $perPage  = 10;
        $pagesCnt = (int)ceil($total / $perPage);

        $pageResult = [
            'items'      => $entries,
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
        $this->assertSame($rows[0]['date'], $items[0]->getDate());
        $this->assertSame($rows[1]['date'], $items[1]->getDate());
        $this->assertSame($rows[2]['date'], $items[2]->getDate());

        $this->assertSame($total,                 $response->getTotal());
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
     * - Four synthetic entries with dates 2025-08-09..12 produced via EntryTestData and Entry::fromArray().
     *
     * Checks:
     * - Items include only 2025-08-12, 2025-08-11, 2025-08-10 in that order.
     * - total/page/perPage/pagesCount reflect the filtered set.
     *
     * @return void
     */
    public function testDateRangeInclusiveReturnsMatchingItems(): void
    {
        // Arrange
        $total = 4;
        $step  = 1;
        $rows = EntryTestData::getMany($total, $step);        

        $entries = [];
        foreach ($rows as $row) {
            $entries[] = Entry::fromArray($row);
        }

        $page     = 1;
        $perPage  = 10;
        $pagesCnt = (int)ceil($total / $perPage);

        $pageResult = [
            'items'      => $entries,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'pagesCount' => $pagesCnt
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

        // Act
        $response = $useCase->execute($request);

        // Assert
        $items = $response->getItems();

        $this->assertCount(4, $items);
        $this->assertSame($rows[0]['date'], $items[0]->getDate());
        $this->assertSame($rows[1]['date'], $items[1]->getDate());
        $this->assertSame($rows[2]['date'], $items[2]->getDate());
        $this->assertSame($rows[3]['date'], $items[3]->getDate());

        $this->assertSame($total,                   $response->getTotal());
        $this->assertSame($request->getPage(),      $response->getPage());
        $this->assertSame($request->getPerPage(),   $response->getPerPage());
        $this->assertSame(1,                        $response->getPagesCount());
    }
}
