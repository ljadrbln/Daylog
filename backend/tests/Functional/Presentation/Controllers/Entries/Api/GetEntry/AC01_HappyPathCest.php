<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\GetEntry;
use Daylog\Tests\Support\Factory\GetEntryTestRequestFactory;
use Daylog\Tests\Support\Scenarios\Entries\GetEntryScenario;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\FunctionalTester;

/**
 * UC-3 / AC-01 — Happy path — Functional.
 *
 * Purpose:
 *   Given a valid existing UUID v4, GET /api/entries/{id} returns 200 with the entry payload.
 *
 * Mechanics:
 *   - Build deterministic dataset via scenario;
 *   - Seed single row;
 *   - GET by id;
 *   - Assert 200 + success=true + data contains seeded fields; no errors key.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\GetEntryController::show
 * @group UC-GetEntry
 */
final class AC01_HappyPathCest extends BaseGetEntryFunctionalCest
{
    /**
     * AC-01: Happy path returns the seeded Entry by id.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testHappyPathReturnsSeededEntry(FunctionalTester $I): void
    {
        // Arrange
        $this->withJsonHeaders($I);

        $dataset  = GetEntryScenario::ac01HappyPath();
        $rows     = $dataset['rows'];
        $targetId = $dataset['targetId'];
        $payload  = GetEntryTestRequestFactory::happyPayload($targetId);

        EntriesSeeding::intoDb($rows);

        // Act        
        $this->getEntry($I, $payload);

        // Assert (HTTP + contract)
        $this->assertOkContract($I);

        // Assert (payload equals seeded)
        $seeded = $rows[0];

        $expectedId = ['data' => ['id' => $seeded['id']]];
        $I->seeResponseContainsJson($expectedId);

        $expectedTitle = ['data' => ['title' => $seeded['title']]];
        $I->seeResponseContainsJson($expectedTitle);

        $expectedBody = ['data' => ['body' => $seeded['body']]];
        $I->seeResponseContainsJson($expectedBody);

        $expectedDate = ['data' => ['date' => $seeded['date']]];
        $I->seeResponseContainsJson($expectedDate);
    }
}
