<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\GetEntry;

use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Datasets\Entries\GetEntryDataset;
use Daylog\Tests\FunctionalTester;

/**
 * UC-3 / AC-01 — Happy path — Functional.
 *
 * Purpose:
 * Validate that GET /api/entries/{id} returns 200 with success=true and a payload
 * equal to the seeded row when a valid existing UUID v4 is requested.
 *
 * Mechanics:
 * - Seed exactly one deterministic row via GetEntryDataset::ac01ExistingId();
 * - Perform GET using canonical helper from the Base class;
 * - Decode data envelope once and assert field-by-field equality;
 * - Validate UUID format and BR-2 invariants at the payload level.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\GetEntryController::show
 * @group UC-GetEntry
 */
final class AC01_HappyPathCest extends BaseGetEntryFunctionalCest
{
    /**
     * AC-01: Happy path returns the seeded Entry by id.
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @return void
     */
    public function testHappyPathReturnsSeededEntry(FunctionalTester $I): void
    {
        // Arrange
        $dataset = GetEntryDataset::ac01ExistingId();
        $this->seedFromDataset($I, $dataset);

        // Act
        $this->getEntryFromDataset($I, $dataset);

        // Assert (HTTP + contract)
        $this->assertOkContract($I);

        // Assert (payload equals seeded)
        $actualEntry = $this->grabTypedDataEnvelope($I);

        /** @var array<string,string> $expectedEntry */
        $expectedEntry = $dataset['rows'][0];

        /** @var string $returnedId */
        $returnedId = $actualEntry['id'];

        $isValid = UuidGenerator::isValid($returnedId);
        $I->assertTrue($isValid);

        $I->assertSame($expectedEntry['id'],        $actualEntry['id']);
        $I->assertSame($expectedEntry['title'],     $actualEntry['title']);
        $I->assertSame($expectedEntry['body'],      $actualEntry['body']);
        $I->assertSame($expectedEntry['date'],      $actualEntry['date']);
        $I->assertSame($expectedEntry['createdAt'], $actualEntry['createdAt']);
        $I->assertSame($expectedEntry['updatedAt'], $actualEntry['updatedAt']);
    }
}
