<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;

/**
 * AC-7: With date=YYYY-MM-DD, only entries with an exact logical date match are returned.
 *
 * Purpose: 
 *   - Ensure the single-date filter returns exclusively entries whose logical 'date' equals the requested value; 
 *   - no other dates leak into results.
 * 
 * Mechanics: 
 *   - Seed 5 entries; 
 *   - set exactly two to the target date; 
 *   - request with date filter; 
 *   - expect exactly those two (order: date DESC) and consistent pagination meta.
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 * 
 * @group UC-ListEntries
 */
final class AC7_SingleDateExactMatchTest extends BaseListEntriesIntegrationTest
{
    /** 
     * AC-7: Single-date filter returns only exact logical-date matches (ordered by date DESC). 
     * 
     * @return void
     */
    public function testSingleDateFilterReturnsOnlyExactMatches(): void
    {
        // Arrange
        $rows = EntryFixture::insertRows(5, 0);

        $targetDate = $rows[0]['date'];
        $otherDate1 = '2025-08-10';
        $otherDate2 = '2025-08-11';
        $otherDate3 = '2025-08-13';

        $id1 = $rows[0]['id'];
        $id2 = $rows[1]['id'];
        $id3 = $rows[2]['id'];
        $id4 = $rows[3]['id'];
        $id5 = $rows[4]['id'];

        EntryFixture::updateById($id3, ['date' => $otherDate1]);
        EntryFixture::updateById($id4, ['date' => $otherDate2]);
        EntryFixture::updateById($id5, ['date' => $otherDate3]);

        // Act
        $data = ListEntriesHelper::getData();
        $data['date'] = $targetDate;

        $request  = ListEntriesHelper::buildRequest($data);
        $response = $this->useCase->execute($request);

        $items = $response->getItems();

        // Collect returned IDs (order-agnostic check)
        $actualIds = [];
        foreach ($items as $item) {
            $actualIds[] = $item['id'];
        }

        $expectedIds = [$id1, $id2];
        sort($expectedIds);
        sort($actualIds);

        // Assert: only exact logical-date matches are returned
        $this->assertSame(2, count($items));
        $this->assertSame($expectedIds, $actualIds);

        // Assert: pagination metadata is consistent
        $this->assertSame(2, $response->getTotal());
        $this->assertSame(1, $response->getPage());
        $this->assertSame(1, $response->getPagesCount());
    }
}
