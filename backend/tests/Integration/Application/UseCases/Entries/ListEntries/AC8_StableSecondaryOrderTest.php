<?php

declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Helper\ListEntriesHelper;
use Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries\BaseListEntriesIntegrationTest;

/**
 * AC-8: When primary sort keys are equal, a stable secondary order by createdAt DESC is applied.
 *
 * Purpose: 
 *   - Guarantee deterministic ordering when the primary sort field yields equal values for items.
 * 
 * Mechanics: 
 *   - Equalize primary key (e.g., date) for all items; 
 *   - set created_at increasing; 
 *   - sort by the primary field; 
 *   - expect order by createdAt DESC as a stable tie-breaker.
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 */
final class AC8_StableSecondaryOrderTest extends BaseListEntriesIntegrationTest
{
    /** 
     * AC-8: Equal primary sort keys fall back to stable secondary order by createdAt DESC. 
     * 
     * @return void 
     */
    public function testStableSecondaryOrderWhenPrimarySortKeysAreEqual(): void
    {
        // Arrange: three entries share the same 'date' (primary key equal by design)
        $rows = EntryFixture::insertRows(3, 0);

        $id1 = $rows[0]['id'];
        $id2 = $rows[1]['id'];
        $id3 = $rows[2]['id'];

        // created_at strictly increasing to make DESC order unambiguous
        $c1 = '2025-08-12 10:00:00';
        $c2 = '2025-08-12 11:00:00';
        $c3 = '2025-08-12 12:00:00';

        // updated_at equalized to avoid affecting the outcome
        $u  = '2025-08-12 13:00:00';

        EntryFixture::updateById($id1, ['created_at' => $c1, 'updated_at' => $u]);
        EntryFixture::updateById($id2, ['created_at' => $c2, 'updated_at' => $u]);
        EntryFixture::updateById($id3, ['created_at' => $c3, 'updated_at' => $u]);

        // Act: primary sort key is 'date' (all equal) => must fall back to createdAt DESC
        $data = ListEntriesHelper::getData();
        $field = 'date';
        $dir   = 'ASC';
        $data['sortField'] = $field;
        $data['sortDir']   = $dir;

        $req   = ListEntriesHelper::buildRequest($data);
        $res   = $this->useCase->execute($req);
        $items = $res->getItems();

        // Assert: stable secondary order by createdAt DESC => id3, id2, id1
        $this->assertSame($id3, $items[0]->getId());
        $this->assertSame($id2, $items[1]->getId());
        $this->assertSame($id1, $items[2]->getId());
    }
}
