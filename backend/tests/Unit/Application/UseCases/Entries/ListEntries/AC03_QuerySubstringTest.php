<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Domain\Models\Entries\Entry;

/**
 * UC-2 / AC-03 — Query substring (case-insensitive) — Unit.
 *
 * Purpose:
 * Ensure a case-insensitive substring query matches either title or body,
 * excludes unrelated entries, and preserves date DESC ordering in results.
 *
 * Mechanics:
 * - Seed fake repo with three entries (dates 2025-08-10..12).
 * - Make title match "Alpha" for D10; body match "aLpHa" for D11; keep D12 unrelated.
 * - Query "alpha" ⇒ expect two hits ordered by date DESC: 2025-08-11 then 2025-08-10.
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries\ListEntries::execute
 * @group UC-ListEntries
 */
final class AC03_QuerySubstringTest extends BaseListEntriesUnitTest
{
    /**
     * Case-insensitive substring query matches title OR body; unrelated entries are excluded.
     *
     * @return void
     */
    public function testQueryFiltersTitleOrBodyCaseInsensitive(): void
    {
        // Arrange: seed three entries
        $repo = $this->makeRepo();

        $data = [
            ['date'  => '2025-08-10', 'title' => 'Alpha note',      'body'  => 'Regular body'],
            ['date'  => '2025-08-11', 'title' => 'Regular title',   'body'  => 'This body has aLpHa inside'],
            ['date'  => '2025-08-12', 'title' => 'Unrelated title', 'body'  => 'Something else']
        ];

        for($i=0; $i<count($data); $i++) {
            $item = $data[$i];

            $entryData = EntryTestData::getOne($item['title'], $item['body'], $item['date']);
            $entry     = Entry::fromArray($entryData);
            $repo->save($entry);
        }

        $query    = 'alpha';
        $request  = ListEntriesTestRequestFactory::fromOverrides(['query' => $query]);
        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        // Act
        $response = $useCase->execute($request);
        $items    = $response->getItems();

        // Assert: two hits (D11 body match; D10 title match), ordered by date DESC
        $this->assertCount(2, $items);
        $this->assertSame($data[1]['date'], $items[0]['date']);
        $this->assertSame($data[0]['date'], $items[1]['date']);
    }
}
