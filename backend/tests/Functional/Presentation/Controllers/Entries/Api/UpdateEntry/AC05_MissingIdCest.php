<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Domain\Services\UuidGenerator;

/**
 * AC-05 (missing id): 422 with ID_REQUIRED.
 *
 * Purpose:
 *   Verify that the API endpoint PUT /api/entries rejects requests without a path id
 *   and returns a transport-level validation error with code ID_REQUIRED.
 *
 * Mechanics:
 *   - Do NOT provide an id in the URL path (PUT /api/entries);
 *   - Provide a minimal valid JSON body (e.g., only 'title') to isolate the missing id error;
 *   - Assert 422 contract and presence of ID_REQUIRED in the errors array.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\UpdateEntry\UpdateEntryController
 * @group UC-UpdateEntry
 */
final class AC05_MissingIdCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * UC-5 / AC-5 — Missing id yields 422 with ID_REQUIRED.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testMissingIdFailsValidationWithIdRequired(FunctionalTester $I): void
    {
        // Arrange — JSON headers and minimal payload without 'id'
        $this->withJsonHeaders($I);
        $payload = UpdateEntryTestRequestFactory::missingIdPayload();

        // Act
        $this->updateEntry($I, $payload);

        // Assert — 422 + ID_REQUIRED per contract
        $this->assertUnprocessableContract($I);

        $code = 'ID_REQUIRED';
        $this->assertErrorCode($I, $code);
    }
}
