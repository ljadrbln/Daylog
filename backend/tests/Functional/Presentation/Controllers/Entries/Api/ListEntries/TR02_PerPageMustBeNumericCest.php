<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * TR-02 — Transport: perPage must be numeric.
 *
 * Purpose:
 * Validate transport-level type check for "perPage": non-numeric inputs must be rejected
 * with 400 Bad Request and code PER_PAGE_MUST_BE_NUMERIC.
 *
 * Not part of UC-2 acceptance criteria.
 *
 * @covers \Daylog\Presentation\Requests\Entries\ListEntries\ListEntriesRequestFactory
 * @group UC-ListEntries
 */
final class TR02_PerPageMustBeNumericCest extends BaseListEntriesFunctionalCest
{
    public function testPerPageMustBeNumeric(FunctionalTester $I): void
    {
        // Arrange
        $dataset = ListEntriesDataset::tr02PerPageMustBeNumeric();

        // Act
        $this->listEntriesFromDataset($I, $dataset);

        // Assert (HTTP contract — 400 + success=false)
        $this->assertBadRequestContract($I);

        // Assert (error code presence and no data)
        $code = 'PER_PAGE_MUST_BE_NUMERIC';
        $this->assertErrorCode($I, $code);
    }
}
