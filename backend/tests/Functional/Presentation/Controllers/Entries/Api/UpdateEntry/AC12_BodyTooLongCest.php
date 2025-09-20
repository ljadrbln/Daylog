<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * AC-12 (body too long): 422 with BODY_TOO_LONG.
 *
 * Purpose:
 *   Verify that PUT /api/entries/{id} rejects a body longer than ENTRY-BR-2 (50000 chars)
 *   and returns a transport-level validation error with code BODY_TOO_LONG.
 *
 * Mechanics:
 *   - Build a payload with a valid UUID id and a body of length 50001 via factory;
 *   - Issue PUT /api/entries/{id} with JSON body;
 *   - Assert HTTP 422 contract and BODY_TOO_LONG in the error payload.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\UpdateEntry\UpdateEntryController
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC12_BodyTooLongCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * UC-5 / AC-12 — Provided body > 50000 chars → 422 BODY_TOO_LONG.
     *
     * This test sends a valid PUT request with an overlong body to ensure
     * transport validation triggers and the API responds with the standardized
     * 422 contract and the expected error code.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testBodyTooLongFailsValidationWithBodyTooLong(FunctionalTester $I): void
    {
        // Arrange
        $dataset = UpdateEntryDataset::ac12TooLongBody();

        // Act
        $this->withJsonHeaders($I);
        $this->updateEntryFromDataset($I, $dataset);

        // Assert (HTTP + contract)
        $this->assertUnprocessableContract($I);

        // Assert (error code)
        $code = 'BODY_TOO_LONG';
        $this->assertErrorCode($I, $code);
    }
}
