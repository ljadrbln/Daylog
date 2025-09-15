<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Daylog\Tests\Support\Factory\ListEntriesTestRequestFactory;

use Daylog\Tests\Support\Scenarios\Entries\ListEntriesScenario;
use Daylog\Tests\Support\Helper\EntriesSeeding;

/**
 * UC-2 / AC-2 — Date range inclusive — Unit.
 *
 * Purpose:
 * Ensure entries equal to dateFrom/dateTo bounds are included and ordering is date DESC.
 *
 * Mechanics:
 * - Seed fake repo with three entries dated 2025-08-10..12.
 * - Build request via ListEntriesTestRequestFactory::rangeInclusive('2025-08-10','2025-08-11').
 * - Expect exactly two results: 2025-08-11, then 2025-08-10.
 *
 * @covers \Daylog\Application\UseCases\Entries\ListEntries\ListEntries::execute
 * @group UC-ListEntries
 */
final class AC02_DateRangeInclusiveTest extends BaseListEntriesUnitTest
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
        $to          = $dataset['to'];
        $from        = $dataset['from'];
        $expectedIds = $dataset['expectedIds'];

        $repo = $this->makeRepo();

        $repo      = $this->makeRepo();
        $request   = ListEntriesTestRequestFactory::rangeInclusive($from, $to);
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
