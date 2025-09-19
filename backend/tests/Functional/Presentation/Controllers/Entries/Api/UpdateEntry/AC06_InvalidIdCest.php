<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * AC-06 (invalid id): 422 with ID_INVALID.
 *
 * Purpose:
 *   Verify that the API endpoint PUT /api/entries/{id} rejects a non-UUID identifier
 *   and returns a transport-level validation error with code ID_INVALID.
 *
 * Mechanics:
 *   - Use an obviously malformed id ('not-a-uuid') in the URL path;
 *   - Provide a minimal valid JSON body (e.g., title only) to isolate the id error;
 *   - Assert 422 contract and presence of ID_INVALID in the errors array.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\UpdateEntry\UpdateEntryController
 * @group UC-UpdateEntry
 */
final class AC06_InvalidIdCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * UC-5 / AC-6 — Non-UUID id yields 422 with ID_INVALID.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testInvalidIdFailsValidationWithIdInvalid(FunctionalTester $I): void
    {
        // Arrange
        $this->withJsonHeaders($I);
        $payload = UpdateEntryTestRequestFactory::invalidIdPayload();

        // Act
        $this->updateEntry($I, $payload);

        // Assert — 422 + ID_INVALID per contract
        $this->assertUnprocessableContract($I);

        $code = 'ID_INVALID';
        $this->assertErrorCode($I, $code);
    }
}
