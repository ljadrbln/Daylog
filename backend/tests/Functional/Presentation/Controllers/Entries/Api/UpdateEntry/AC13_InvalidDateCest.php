<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * AC-13 (invalid date): 422 with DATE_INVALID.
 *
 * Purpose:
 *   Verify that PUT /api/entries/{id} rejects malformed or non-existent calendar dates
 *   and returns a transport-level validation error with code DATE_INVALID.
 *
 * Mechanics:
 *   - Build a payload with a valid UUID id and an invalid 'date' (format or impossible calendar value) via factory;
 *   - Issue PUT /api/entries/{id} with JSON body;
 *   - Assert HTTP 422 contract and DATE_INVALID in the error payload.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\UpdateEntry\UpdateEntryController
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC13_InvalidDateCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * UC-5 / AC-13 — Invalid date (malformed or impossible) → 422 DATE_INVALID.
     *
     * Sends a PUT with an invalid 'date' value and expects standardized transport validation:
     * HTTP 422 with error code DATE_INVALID.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testInvalidDateFailsValidationAndReturnsDateInvalid(FunctionalTester $I): void
    {
        // Arrange
        $dataset = UpdateEntryDataset::ac13InvalidDate();

        // Act
        $this->updateEntryFromDataset($I, $dataset);

        // Assert (HTTP + contract)
        $this->assertUnprocessableContract($I);

        // Assert (error code)
        $code = 'DATE_INVALID';
        $this->assertErrorCode($I, $code);
    }
}
