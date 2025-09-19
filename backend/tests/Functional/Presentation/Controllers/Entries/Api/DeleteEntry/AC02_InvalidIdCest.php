<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\DeleteEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Factory\DeleteEntryTestRequestFactory;

/**
 * UC-4 / AC-02 — Invalid id — Functional.
 *
 * Purpose:
 *   Non-UUID id must yield 422 with ID_INVALID.
 *
 * Mechanics:
 *   - Use a clearly invalid id string;
 *   - DELETE /api/entries/{id};
 *   - Assert 422 contract and ID_INVALID code in response.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\DeleteEntryController::delete
 * @group UC-DeleteEntry
 */
final class AC02_InvalidIdCest extends BaseDeleteEntryFunctionalCest
{
    /**
     * AC-02: Invalid id → 422 with ID_INVALID.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testInvalidIdFailsWithIdInvalid(FunctionalTester $I): void
    {
        // Arrange
        $this->withJsonHeaders($I);

        $payload = DeleteEntryTestRequestFactory::invalidIdPayload();

        // Act
        $this->deleteEntry($I, $payload);

        // Assert
        $this->assertUnprocessableContract($I);

        $code = 'ID_INVALID';
        $this->assertErrorCode($I, $code);
    }
}
