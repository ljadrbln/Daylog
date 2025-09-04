<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;

 /**
  * AC-2: dateFrom/dateTo are inclusive by logical 'date' (YYYY-MM-DD).
  *
  * Purpose:
  *   - Prove that entries equal to the range bounds are included and the default ordering remains date DESC.
  *
  * Mechanics:
  *   - Seed three entries dated 2025-08-10, 2025-08-11, 2025-08-12.
  *   - Call with dateFrom=2025-08-10, dateTo=2025-08-11.
  *   - Expect exactly two results: 2025-08-11, then 2025-08-10.
  *
  * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
  * @covers \Daylog\Application\UseCases\Entries\ListEntries
  *
  * @group UC-ListEntries
  */
final class AC2_DateRangeInclusiveTest extends BaseListEntriesIntegrationTest
{
    /** 
     * AC-2: Inclusive [dateFrom..dateTo] returns boundary items ordered by date DESC. 
     * 
     * @return void 
     */
    public function testDateRangeInclusiveReturnsMatchingItems(): void
    {
        // Arrange: insert three rows via fixture
        $rows = EntryFixture::insertRows(3, 1);

        // Request with inclusive date range [[0]..[1]]
        $data             = ListEntriesHelper::getData();
        $data['dateFrom'] = $rows[0]['date'];
        $data['dateTo']   = $rows[1]['date'];

        $request  = ListEntriesHelper::buildRequest($data);

        // Act
        $response = $this->useCase->execute($request);

        // Assert
        $items = $response->getItems();

        $this->assertCount(2, $items);
        $this->assertSame($data['dateTo'],   $items[0]->getDate());
        $this->assertSame($data['dateFrom'], $items[1]->getDate());
    }
}
