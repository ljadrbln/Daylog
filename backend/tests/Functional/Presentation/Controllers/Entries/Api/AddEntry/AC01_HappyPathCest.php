<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\AddEntry;

use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\AddEntryDataset;

/**
 * UC-1 / AC-01 — Happy path — Functional.
 *
 * Purpose:
 *   Given a valid (title, body, date) payload, POST /api/entries returns 200 with
 *   success=true and code=OK (project contract), and the new entry is persisted.
 *
 * Mechanics:
 *   - Build canonical payload via factory;
 *   - POST as JSON using the base helper;
 *   - Assert 200 + success=true + code=OK;
 *   - Extract returned id, assert it is UUID v4;
 *   - Assert DB now contains the entry by id.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\AddEntryController::create
 * @group UC-AddEntry
 */
final class AC01_HappyPathCest extends BaseAddEntryFunctionalCest
{
    /**
     * AC-01: Happy path creates a new Entry and returns its id.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testHappyPathCreatesEntryAndReturnsId(FunctionalTester $I): void
    {
        // Arrange
        $dataset = AddEntryDataset::ac01HappyPath();

        // Act
        $this->addEntryFromDataset($I, $dataset);

        // Assert (HTTP + contract)
        $this->assertOkContract($I);

        // Assert (response contains a valid UUID id)
        $responseData = $this->grabTypedDataEnvelope($I);
        $entryId      = $responseData['id'];

        $isValid = UuidGenerator::isValid($entryId);
        $I->assertTrue($isValid);

        // Assert (DB contains the created entry)
        $exists = EntryFixture::existsById($entryId);
        $I->assertTrue($exists);
    }
}
