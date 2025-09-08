<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;

/**
 * AC-3: Query filters by substring in title OR body (case-insensitive).
 *
 * Purpose: 
 *   - Ensure a case-insensitive substring matches either field and unrelated entries are excluded; 
 *   - results remain ordered by date DESC.
 * 
 * Mechanics: 
 *   - Seed 3 entries; 
 *   - set title match "Alpha" for $rows[0], body match "aLpHa" for $rows[1], keep $rows[2] unrelated; 
 *   - query="alpha" ⇒ expect two hits ordered by date DESC.
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 * 
 * @group UC-ListEntries
 */
final class AC3_QuerySubstringTest extends BaseListEntriesIntegrationTest
{
    /** 
     * AC-3: Case-insensitive substring query matches title or body; unrelated entries excluded; expect two hits ordered by date DESC. 
     * 
     * @return void 
     */
    public function testQueryFiltersTitleOrBodyCaseInsensitive(): void
    {
        // Arrange: seed three dates via fixture
        $rows = EntryFixture::insertRows(3, 1);

        $id1 = $rows[0]['id'];
        $id2 = $rows[1]['id'];

        // Adjust specific rows to craft title/body matches for query
        EntryFixture::updateById($id1, ['title' => 'Alpha note']);
        EntryFixture::updateById($id2, ['body'  => 'This body has aLpHa inside']);

        // Request with query
        $data  = ListEntriesHelper::getData();
        $query = 'alpha';
        $data['query'] = $query;

        $request  = ListEntriesHelper::buildRequest($data);
        $response = $this->useCase->execute($request);

        // Assert: two hits (body match; title match), ordered by date DESC
        $items = $response->getItems();

        $this->assertCount(2, $items);
        $this->assertSame($rows[1]['date'], $items[0]['date']);
        $this->assertSame($rows[0]['date'], $items[1]['date']);
    }
}
