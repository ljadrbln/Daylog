<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

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
        $dataset = ListEntriesDataset::ac01HappyPath();
        $this->seedFromDataset($dataset);

        // Act
        $request  = $dataset['request'];
        $response = $this->useCase->execute($request);
        $items    = $response->getItems();
        
        // Assert
        $this->assertSame(3, count($items));

        $actualIds   = array_column($items, 'id');
        $expectedIds = $dataset['expectedIds'];

        $this->assertSame($expectedIds, $actualIds);
    }
}