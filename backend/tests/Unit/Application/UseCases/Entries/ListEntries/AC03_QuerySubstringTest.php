<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Domain\Models\Entries\Entry;

use Daylog\Tests\Support\Scenarios\Entries\ListEntriesScenario;
use Daylog\Tests\Support\Helper\EntriesSeeding;

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
        // Arrange
        $dataset = ListEntriesScenario::ac03QueryTitleOrBodyCaseInsensitive();
        
        $rows        = $dataset['rows'];
        $query       = $dataset['query'];
        $expectedIds = $dataset['expectedIds'];

        $repo      = $this->makeRepo();
        $request   = ListEntriesTestRequestFactory::fromOverrides(['query' => $query]);
        $validator = $this->makeValidatorOk();
        $useCase   = $this->makeUseCase($repo, $validator);

        EntriesSeeding::intoFakeRepo($repo, $rows);

        // Act
        $response = $useCase->execute($request);
        $items    = $response->getItems();

        // Assert
        $this->assertCount(2, $items);

        $actualIds = array_column($items, 'id');
        $this->assertSame($expectedIds, $actualIds);
    }
}
