<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\DeleteEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\DeleteEntryDataset;

/**
 * UC-4 / AC-03 — Not found — Functional.
 *
 * Purpose:
 *   Valid UUID that doesn't exist must yield 404 with ENTRY_NOT_FOUND.
 *
 * Mechanics:
 *   - Use scenario to obtain a valid UUID that is not inserted;
 *   - DELETE /api/entries/{id};
 *   - Assert 404 contract and ENTRY_NOT_FOUND code in response.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\DeleteEntryController::delete
 * @group UC-DeleteEntry
 */
final class AC03_NotFoundCest extends BaseDeleteEntryFunctionalCest
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
        $dataset = DeleteEntryDataset::ac03NotFound();
        $this->seedFromDataset($I, $dataset);

        // Act
        $this->deleteEntryFromDataset($I, $dataset);

        // Assert
        $this->assertNotFoundContract($I);

        $code = 'ENTRY_NOT_FOUND';
        $this->assertErrorCode($I, $code);
    }
}
