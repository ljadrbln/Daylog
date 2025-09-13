<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;

/**
 * UC-2 / AC-03 — Query substring (case-insensitive) — Integration.
 *
 * Purpose:
 * Verify that a case-insensitive substring query matches either title or body,
 * excludes unrelated entries, and the results remain ordered by date DESC.
 *
 * Mechanics:
 * - Seed 3 entries (dates 2025-08-10..12).
 * - Set title match "Alpha" for row[0], body match "aLpHa" for row[1], keep row[2] unrelated.
 * - Query "alpha" ⇒ expect two hits ordered by date DESC.
 *
 * @covers \Daylog\Configuration\Providers\Entries\ListEntriesProvider
 * @covers \Daylog\Application\UseCases\Entries\ListEntries
 *
 * @group UC-ListEntries
 */
final class AC03_QuerySubstringTest extends BaseListEntriesIntegrationTest
{
    /**
     * Case-insensitive substring query matches title OR body; unrelated entries excluded.
     *
     * @return void
     */
    public function testQueryFiltersTitleOrBodyCaseInsensitive(): void
    {
        // Arrange: seed three dates via fixture
        $rows = EntryFixture::insertRows(3, 1);

        $id1 = $rows[0]['id'];
        $id2 = $rows[1]['id'];

        // Craft matches
        $title = 'Alpha note';
        EntryFixture::updateById($id1, ['title' => $title]);

        $body  = 'This body has aLpHa inside';
        EntryFixture::updateById($id2, ['body'  => $body]);

        // Build request via test factory
        $query   = 'alpha';
        $request = ListEntriesTestRequestFactory::query($query);

        // Act
        $response = $this->useCase->execute($request);
        $items    = $response->getItems();

        // Assert: two hits (body match; title match), ordered by date DESC
        $this->assertCount(2, $items);
        $this->assertSame($rows[1]['date'], $items[0]['date']);
        $this->assertSame($rows[0]['date'], $items[1]['date']);
    }
}
