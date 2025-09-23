<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * TR-04 — Transport: sortDir must be string.
 *
 * Purpose:
 * Validate that "sortDir" rejects non-string inputs with 400 and DIRECTION_MUST_BE_STRING.
 *
 * Not part of UC-2 acceptance criteria.
 *
 * @covers \Daylog\Presentation\Requests\Entries\ListEntries\ListEntriesRequestFactory
 * @group UC-ListEntries
 */
final class TR04_SortDirMustBeStringCest extends BaseListEntriesFunctionalCest
{
    public function testSortDirMustBeString(FunctionalTester $I): void
    {
        // Arrange
        $dataset = ListEntriesDataset::tr04SortDirMustBeString();

        // Act
        $this->listEntriesFromDataset($I, $dataset);

        // Assert (HTTP contract — 400 + success=false)
        $this->assertBadRequestContract($I);

        // Assert (error code presence and no data)
        $code = 'DIRECTION_MUST_BE_STRING';
        $this->assertErrorCode($I, $code);
    }
}
