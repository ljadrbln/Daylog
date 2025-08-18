<?php

declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases;

use Codeception\Test\Unit;
use Daylog\Application\UseCases\Entries\ListEntries;
use Daylog\Application\DTO\Entries\ListEntriesRequest;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Tests\Support\Helper\EntryHelper;

/**
 * Unit test for UC-2 ListEntries.
 *
 * Scenario: repository has multiple entries with different dates.
 * Expectation: UseCase returns them ordered by date DESC with pagination metadata.
 *
 * @covers ListEntries
 */
final class ListEntriesTest extends Unit
{
    /**
     * Ensures that ListEntries returns entries sorted by date DESC
     * when no filters are provided (AC-1 happy path).
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

        $useCase = new ListEntries($repo);
        $request = new ListEntriesRequest();

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
}
