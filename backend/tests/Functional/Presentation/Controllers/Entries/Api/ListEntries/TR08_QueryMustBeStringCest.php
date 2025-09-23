<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\ListEntriesDataset;

/**
 * TR-08 — Transport: query must be string.
 *
 * Purpose:
 * Validate that "query" rejects non-string inputs with 400 and QUERY_MUST_BE_STRING.
 *
 * Not part of UC-2 acceptance criteria.
 *
 * @covers \Daylog\Presentation\Requests\Entries\ListEntries\ListEntriesRequestFactory
 * @group UC-ListEntries
 */
final class TR08_QueryMustBeStringCest extends BaseListEntriesFunctionalCest
{
    public function testQueryMustBeString(FunctionalTester $I): void
    {
        // Arrange
        $dataset = ListEntriesDataset::tr08QueryMustBeString();

        // Act
        $this->listEntriesFromDataset($I, $dataset);

        // Assert (HTTP contract — 400 + success=false)
        $this->assertBadRequestContract($I);

        // Assert (error code presence and no data)
        $code = 'QUERY_MUST_BE_STRING';
        $this->assertErrorCode($I, $code);
    }
}
