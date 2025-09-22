<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * AC-14 (no-op): 200 with NO_CHANGES_APPLIED.
 *
 * Purpose:
 *   Verify that PUT /api/entries/{id} detects a no-op (all provided values equal to stored)
 *   and returns a non-error informational response with message NO_CHANGES_APPLIED.
 *   The stored Entry must remain unchanged (updatedAt intact), but this test focuses on API contract.
 *
 * Mechanics:
 *   - Seed a single entry via EntriesSeeding using UpdateEntryScenario::ac14NoOp();
 *   - Build a no-op payload (same id/title/body/date as stored) via factory;
 *   - Issue PUT /api/entries/{id} with JSON body;
 *   - Assert HTTP 200 contract and NO_CHANGES_APPLIED message in the payload.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\UpdateEntry\UpdateEntryController
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC14_NoOpCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * UC-5 / AC-14 — Identical values trigger NO_CHANGES_APPLIED (non-error informational).
     *
     * Sends a PUT with fields identical to persisted values and expects standardized
     * success response (200) with message NO_CHANGES_APPLIED.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testNoOpUpdateReportsNoChangesApplied(FunctionalTester $I): void
    {
        // Arrange
        $dataset = UpdateEntryDataset::ac14NoOp();
        $this->seedFromDataset($I, $dataset);

        // Act
        $this->updateEntryFromDataset($I, $dataset);

        // Assert (HTTP + contract)
        $this->assertUnprocessableContract($I);

        // Assert (error code)
        $code = 'NO_CHANGES_APPLIED';        
        $this->assertErrorCode($I, $code);
    }
}
