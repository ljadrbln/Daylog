<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\DeleteEntry;

use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\DeleteEntryDataset;


/**
 * UC-4 / AC-01 — Happy path — Functional.
 *
 * Purpose:
 *   Given a valid existing UUID v4, DELETE /api/entries/{id} returns 200 with
 *   success=true and code=OK, and the entry is removed from storage.
 *
 * Mechanics:
 *   - Build deterministic dataset via scenario;
 *   - Seed single row;
 *   - DELETE by id;
 *   - Assert 200 + success=true + code=OK; data contains id of deleted entry;
 *   - Assert that entry no longer exists in DB.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\DeleteEntryController::delete
 * @group UC-DeleteEntry
 */
final class AC01_HappyPathCest extends BaseDeleteEntryFunctionalCest
{
    /**
     * AC-01: Happy path deletes the seeded Entry by id.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testHappyPathDeletesSeededEntry(FunctionalTester $I): void
    {
        // Arrange
        $dataset = DeleteEntryDataset::ac01ExistingId();
        $this->seedFromDataset($I, $dataset);

        // Act
        $this->deleteEntryFromDataset($I, $dataset);

        // Assert (HTTP + contract)
        $this->assertOkContract($I);

        // Assert (payload contains id of deleted entry)
        $targetId   = $dataset['payload']['id'];
        $expectedId = ['data' => ['id' => $targetId]];
        $I->seeResponseContainsJson($expectedId);

        // Assert (DB no longer contains entry)
        $exists = EntryFixture::existsById($targetId);
        $I->assertFalse($exists, 'Entry must be deleted from database.');
    }
}
