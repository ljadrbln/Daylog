<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * AC-10 (title too long): 422 with TITLE_TOO_LONG.
 *
 * Purpose:
 *   Verify that PUT /api/entries/{id} rejects a title longer than ENTRY-BR-1 (200 chars)
 *   and returns a transport-level validation error with code TITLE_TOO_LONG.
 *
 * Mechanics:
 *   - Build a payload with a valid UUID id and a title of length 201 via factory;
 *   - Issue PUT /api/entries/{id} with JSON body;
 *   - Assert HTTP 422 contract and TITLE_TOO_LONG in the error payload.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\UpdateEntry\UpdateEntryController
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC10_TitleTooLongCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * UC-5 / AC-10 — Provided title > 200 chars → 422 TITLE_TOO_LONG.
     *
     * This test sends a valid PUT request with an overlong title to ensure
     * transport validation triggers and the API responds with the standardized
     * 422 contract and the expected error code.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testTitleTooLongFailsValidationWithTitleTooLong(FunctionalTester $I): void
    {
        // Arrange
        $dataset = UpdateEntryDataset::ac10TooLongTitle();

        // Act
        $this->updateEntryFromDataset($I, $dataset);

        // Assert (HTTP + contract)
        $this->assertUnprocessableContract($I);

        // Assert (error code)
        $code = 'TITLE_TOO_LONG';
        $this->assertErrorCode($I, $code);
    }
}
