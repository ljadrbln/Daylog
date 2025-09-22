<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\GetEntry;

use Daylog\Tests\Support\Scenarios\Entries\GetEntryScenario;
use Daylog\Tests\Support\Factory\GetEntryTestRequestFactory;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\GetEntryDataset;

/**
 * UC-3 / AC-03 — Not found — Functional.
 *
 * Purpose:
 *   Valid UUID that doesn't exist must yield 404 with ENTRY_NOT_FOUND.
 *
 * Mechanics:
 *   - Use scenario to obtain a valid UUID that is not inserted;
 *   - GET /api/entries/{id};
 *   - Assert 404 contract and ENTRY_NOT_FOUND in errors.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\GetEntryController::show
 * @group UC-GetEntry
 */
final class AC03_NotFoundCest extends BaseGetEntryFunctionalCest
{
    /**
     * AC-03: Non-existent id → 404 with ENTRY_NOT_FOUND.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testNotFoundFailsWithEntryNotFound(FunctionalTester $I): void
    {
        // Arrange
        $dataset = GetEntryDataset::ac03NotFound();
        $this->seedFromDataset($I, $dataset);
        
        // Act
        $this->withJsonHeaders($I);
        $this->getEntryFromDataset($I, $dataset);

        // Assert
        $this->assertNotFoundContract($I);

        $code = 'ENTRY_NOT_FOUND';
        $this->assertErrorCode($I, $code);
    }
}
