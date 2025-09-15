<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;
use Daylog\Tests\Support\Scenarios\Entries\ListEntriesScenario;
use Daylog\Tests\Support\Helper\EntriesSeeding;

/**
 * UC-2 / AC-2 — Date range inclusive — Integration.
 *
 * Purpose:
 * Prove that entries equal to the range bounds are included and ordering remains date DESC.
 *
 * Mechanics:
 * - Seed three entries dated 2025-08-10..12 via EntryFixture.
 * - Build request via ListEntriesTestRequestFactory::rangeInclusive(dateFrom, dateTo).
 * - Expect exactly two results: 11th then 10th.
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 *
 * @group UC-ListEntries
 */
final class AC02_DateRangeInclusiveTest extends BaseListEntriesIntegrationTest
{
    /**
     * Inclusive [dateFrom..dateTo] returns boundary items ordered by date DESC.
     *
     * @return void
     */
    public function testDateRangeInclusiveReturnsMatchingItems(): void
    {
        // Arrange
        $dataset = ListEntriesScenario::ac02DateRangeInclusive();

        $rows        = $dataset['rows'];
        $dateFrom    = $dataset['from'];        
        $dateTo      = $dataset['to'];        
        $expectedIds = $dataset['expectedIds'];
        
        $request = ListEntriesTestRequestFactory::rangeInclusive($dateFrom, $dateTo);
        EntriesSeeding::intoDb($rows);

        // Act
        $response  = $this->useCase->execute($request);
        $items     = $response->getItems();
        
        // Assert
        $this->assertSame(2, count($items));

        $actualIds = array_column($items, 'id');
        $this->assertSame($expectedIds, $actualIds);
    }
}
