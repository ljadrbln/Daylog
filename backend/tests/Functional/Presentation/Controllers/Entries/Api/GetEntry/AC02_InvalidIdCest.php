<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\GetEntry;
use Daylog\Tests\FunctionalTester;

/**
 * UC-3 / AC-02 — Invalid id — Functional.
 *
 * Purpose:
 *   Non-UUID id must yield 422 with ID_INVALID.
 *
 * Mechanics:
 *   - Use a clearly invalid id string;
 *   - GET /api/entries/{id};
 *   - Assert 422 contract and ID_INVALID in errors.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\GetEntryController::show
 * @group UC-GetEntry
 */
final class AC02_InvalidIdCest extends BaseGetEntryFunctionalCest
{
    /**
     * AC-02: Invalid id → 422 with ID_INVALID.
     *
     * @param \FunctionalTester $I
     * @return void
     */
    public function testInvalidIdFailsWithIdInvalid(FunctionalTester $I): void
    {
        // Arrange
        $this->withJsonHeaders($I);

        $id = 'not-a-uuid';

        // Act
        $this->getEntry($I, $id);

        // Assert
        $this->assertUnprocessableContract($I);

        $code = 'ID_INVALID';
        $this->assertErrorCode($I, $code);
    }
}
