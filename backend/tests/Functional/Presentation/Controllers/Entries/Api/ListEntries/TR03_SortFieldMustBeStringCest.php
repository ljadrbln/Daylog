<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * TR-03 — Transport: sortField must be string.
 *
 * Purpose:
 * Validate that "sortField" rejects non-string inputs with 400 and SORT_FIELD_MUST_BE_STRING.
 *
 * Not part of UC-2 acceptance criteria.
 *
 * @covers \Daylog\Presentation\Requests\Entries\ListEntries\ListEntriesRequestFactory
 * @group UC-ListEntries
 */
final class TR03_SortFieldMustBeStringCest extends BaseListEntriesFunctionalCest
{
    public function testSortFieldMustBeString(FunctionalTester $I): void
    {
        // Arrange
        $dataset = ListEntriesDataset::tr03SortFieldMustBeString();

        // Act
        $this->listEntriesFromDataset($I, $dataset);

        // Assert (HTTP contract — 400 + success=false)
        $this->assertBadRequestContract($I);

        // Assert (error code presence and no data)
        $code = 'SORT_FIELD_MUST_BE_STRING';
        $this->assertErrorCode($I, $code);
    }
}
