<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;

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
        $rows = EntryFixture::insertRows(3, 1);

        $dateFrom = $rows[0]['date'];
        $dateTo   = $rows[1]['date'];

        $request = ListEntriesTestRequestFactory::rangeInclusive($dateFrom, $dateTo);

        // Act
        $response = $this->useCase->execute($request);
        $items    = $response->getItems();

        // Assert
        $this->assertCount(2, $items);

        $actualDateTo   = $items[0]['date'];
        $actualDateFrom = $items[1]['date'];

        $this->assertSame($dateTo,   $actualDateTo);
        $this->assertSame($dateFrom, $actualDateFrom);
    }
}
