<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * TR-01 — Transport: page must be numeric.
 *
 * Purpose:
 * Validate transport-level type check for "page": non-numeric inputs must be rejected
 * at the HTTP boundary with 400 Bad Request and code PAGE_MUST_BE_NUMERIC.
 *
 * Mechanics:
 * - Build a request where "page" is a non-numeric type (e.g., array);
 * - GET /api/entries via base helper;
 * - Assert 400 + success=false and PAGE_MUST_BE_NUMERIC in errors.
 *
 * Not part of UC-2 acceptance criteria; this is transport validation.
 *
 * @covers \Daylog\Presentation\Requests\Entries\ListEntries\ListEntriesRequestFactory
 * @group UC-ListEntries
 */
final class TR01_PageMustBeNumericCest extends BaseListEntriesFunctionalCest
{
    public function testPageMustBeNumeric(FunctionalTester $I): void
    {
        // Arrange
        $dataset = ListEntriesDataset::tr01PageMustBeNumeric();

        // Act
        $this->listEntriesFromDataset($I, $dataset);

        // Assert
        $this->assertBadRequestContract($I);

        // Assert (error code presence and no data)
        $code = 'PAGE_MUST_BE_NUMERIC';
        $this->assertErrorCode($I, $code);
    }
}
