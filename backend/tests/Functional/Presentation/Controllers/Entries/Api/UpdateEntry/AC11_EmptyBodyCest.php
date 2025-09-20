<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * AC-11 (empty body): 422 with BODY_REQUIRED.
 *
 * Purpose:
 *   Verify that PUT /api/entries/{id} rejects an explicitly provided empty body
 *   (after trimming) and returns a transport-level validation error with code BODY_REQUIRED.
 *
 * Mechanics:
 *   - Build a payload with a valid UUID id and a whitespace-only body via factory;
 *   - Issue PUT /api/entries/{id} with JSON body;
 *   - Assert HTTP 422 contract and BODY_REQUIRED in the error payload.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\UpdateEntry\UpdateEntryController
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC11_EmptyBodyCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * UC-5 / AC-11 — Provided empty (after trimming) body → 422 BODY_REQUIRED.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testEmptyBodyFailsValidationWithBodyRequired(FunctionalTester $I): void
    {
        // Arrange
        $dataset = UpdateEntryDataset::ac11EmptyBody();

        // Act
        $this->withJsonHeaders($I);
        $this->updateEntryFromDataset($I, $dataset);

        // Assert (HTTP + contract)
        $this->assertUnprocessableContract($I);

        // Assert (error code)
        $code = 'BODY_REQUIRED';
        $this->assertErrorCode($I, $code);
    }
}
