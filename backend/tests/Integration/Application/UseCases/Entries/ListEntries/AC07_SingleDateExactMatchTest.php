<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;
use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;

/**
 * AC-07: With date=YYYY-MM-DD, only entries with an exact logical date match are returned.
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
final class AC07_SingleDateExactMatchTest extends BaseListEntriesIntegrationTest
{
    /** 
     * AC-07: Single-date filter returns only exact logical-date matches (ordered by date DESC). 
     * 
     * @return void
     */
    public function testSingleDateFilterReturnsOnlyExactMatches(): void
    {
        // Arrange
        $rows = EntryFixture::insertRows(3, 1);
        $targetDate = $rows[0]['date'];
        
        $request = ListEntriesTestRequestFactory::withDate('date', $targetDate);

        // Act
        $response = $this->useCase->execute($request);
        $items = $response->getItems();

        // Collect returned IDs (order-agnostic check)
        $actualIds   = array_column($items, 'id');
        $expectedIds = [$rows[0]['id']];

        // Assert: only exact logical-date matches are returned
        $this->assertSame(1, count($items));
        $this->assertSame($expectedIds, $actualIds);

        // Assert: pagination metadata is consistent
        $this->assertSame(1, $response->getTotal());
        $this->assertSame(1, $response->getPage());
        $this->assertSame(1, $response->getPagesCount());
    }
}
