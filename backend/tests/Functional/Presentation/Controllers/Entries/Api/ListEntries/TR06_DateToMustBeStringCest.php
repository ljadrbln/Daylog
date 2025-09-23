<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * TR-06 — Transport: dateTo must be string.
 *
 * Purpose:
 * Validate that "dateTo" rejects non-string inputs with 400 and DATE_TO_MUST_BE_STRING.
 *
 * Not part of UC-2 acceptance criteria.
 *
 * @covers \Daylog\Presentation\Requests\Entries\ListEntries\ListEntriesRequestFactory
 * @group UC-ListEntries
 */
final class TR06_DateToMustBeStringCest extends BaseListEntriesFunctionalCest
{
    public function testDateToMustBeString(FunctionalTester $I): void
    {
        // Arrange
        $dataset = ListEntriesDataset::tr06DateToMustBeString();

        // Act
        $this->listEntriesFromDataset($I, $dataset);

        // Assert (HTTP contract — 400 + success=false)
        $this->assertBadRequestContract($I);

        // Assert (error code presence and no data)
        $code = 'DATE_TO_MUST_BE_STRING';
        $this->assertErrorCode($I, $code);
    }
}
