<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\GetEntry;

use Daylog\Tests\Support\Scenarios\Entries\GetEntryScenario;
use Daylog\Tests\Support\Factory\GetEntryTestRequestFactory;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\FunctionalTester;

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
        $this->withJsonHeaders($I);

        $dataset = GetEntryScenario::ac01HappyPath();
        $rows    = $dataset['rows'];

        EntriesSeeding::intoDb($rows);
        $payload = GetEntryTestRequestFactory::notFoundPayload();

        // Act
        $this->getEntry($I, $payload);

        // Assert
        $this->assertNotFoundContract($I);

        $code = 'ENTRY_NOT_FOUND';
        $this->assertErrorCode($I, $code);
    }
}
