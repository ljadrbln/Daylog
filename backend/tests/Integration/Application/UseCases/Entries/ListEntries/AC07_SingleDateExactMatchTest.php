<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;
use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;
use Daylog\Tests\Support\Scenarios\Entries\ListEntriesScenario;
use Daylog\Tests\Support\Helper\EntriesSeeding;

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
        $dataset = ListEntriesScenario::ac07SingleDateExact();
    
        $rows        = $dataset['rows'];
        $targetDate  = $dataset['targetDate'];        
        $expectedIds = $dataset['expectedIds'];
        
        $request = ListEntriesTestRequestFactory::withDate('date', $targetDate);
        EntriesSeeding::intoDb($rows);

        // Act
        $response  = $this->useCase->execute($request);
        $items     = $response->getItems();
        $actualIds = array_column($items, 'id');

        // Assert
        $this->assertSame(2, count($items));
        $this->assertSame($expectedIds, $actualIds);
        $this->assertSame(2, $response->getTotal());
        $this->assertSame(1, $response->getPage());
        $this->assertSame(1, $response->getPagesCount());
    }
}
