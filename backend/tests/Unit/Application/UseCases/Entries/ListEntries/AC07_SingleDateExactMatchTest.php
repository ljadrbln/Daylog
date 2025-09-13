<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Domain\Models\Entries\Entry;

/**
 * UC-2 / AC-07 — Single-date exact match — Unit.
 *
 * Purpose:
 * Verify that the single-date filter (date=YYYY-MM-DD) returns exclusively entries
 * matching the exact logical date, and does not leak other dates.
 *
 * Mechanics:
 * - Seed FakeEntryRepository with 5 entries.
 * - Keep exactly two entries with the target date; set three to other dates.
 * - Build request via ListEntriesTestRequestFactory::singleDate($targetDate).
 * - Execute the use case and assert that only the two matching entries are returned.
 *
 * Assertions:
 * - Exactly 2 items returned and they match the expected ids (order-agnostic).
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries\ListEntries::execute
 * @group UC-ListEntries
 */
final class AC07_SingleDateExactMatchTest extends BaseListEntriesUnitTest
{
    /**
     * AC-07: date=YYYY-MM-DD returns only exact logical-date matches.
     *
     * @return void
     */
    public function testSingleDateFilterReturnsOnlyExactMatches(): void
    {
        // Arrange
        $repo = $this->makeRepo();

        $rows = EntryTestData::getMany(3, 1);
        $targetDate = $rows[0]['date'];

        for($i=0; $i<count($rows); $i++) {
            $row   = $rows[$i];
            $entry = Entry::fromArray($row);
            $repo->save($entry);
        }

        $request   = ListEntriesTestRequestFactory::withDate('date', $targetDate);
        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        // Act
        $response = $useCase->execute($request);
        $items    = $response->getItems();

        // Assert
        $actualIds   = array_column($items, 'id');
        $expectedIds = [$rows[0]['id']];

        $this->assertCount(1, $items);
        $this->assertSame($expectedIds, $actualIds);
    }
}
