<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;
use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;
use Daylog\Tests\Support\Scenarios\Entries\ListEntriesScenario;
use Daylog\Tests\Support\Helper\EntriesSeeding;

/**
 * AC-1: With no filters, the first page is returned, sorted by date DESC by default.
 *
 * Purpose:
 *   - Verify default ordering by logical date (DESC) and consistent pagination metadata
 *   - using real wiring (Provider + SqlFactory) and a clean DB prepared in the base class.
 *
 * Mechanics:
 *   - Insert three entries with consecutive logical dates.
 *   - Execute use case with default request (no filters).
 *   - Assert: order is date3, date2, date1; pagination meta is consistent.
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 * 
 * @group UC-ListEntries
 */
final class AC01_HappyPathTest extends BaseListEntriesIntegrationTest
{
    /**
     * AC-1 Happy path: returns first page sorted by date DESC with correct meta.
     *
     * @return void
     */    
    public function testHappyPathReturnsFirstPageSortedByDateDesc(): void
    {
        // Arrange
        $dataset = ListEntriesScenario::ac01HappyPath();

        $rows        = $dataset['rows'];
        $expectedIds = $dataset['expectedIds'];
        
        $request = ListEntriesTestRequestFactory::happy();
        EntriesSeeding::intoDb($rows);

        // Act
        $response  = $this->useCase->execute($request);
        $items     = $response->getItems();
        
        // Assert
        $this->assertSame(3, count($items));

        $actualIds = array_column($items, 'id');
        $this->assertSame($expectedIds, $actualIds);
    }
}