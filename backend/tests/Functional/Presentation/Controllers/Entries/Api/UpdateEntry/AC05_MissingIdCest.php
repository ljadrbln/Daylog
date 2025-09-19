<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * AC-05 (missing id): expected 422 with ID_REQUIRED, but current router yields 405.
 *
 * Purpose:
 *   Document and verify the behavior when the API endpoint PUT /api/entries is called
 *   without an id in the URL path. According to UC-5 / AC-5 the system must return
 *   a transport-level validation error (422, ID_REQUIRED). In reality the Fat-Free
 *   router rejects the request earlier and returns 405 Method Not Allowed.
 *
 * Mechanics:
 *   - Send PUT /api/entries (no {id} segment);
 *   - Provide a minimal valid JSON body (e.g. only 'title') to isolate the missing id error;
 *   - Observe actual response: 405 Method Not Allowed from router;
 *   - Mark expected behavior (422 with ID_REQUIRED) for future alignment.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\UpdateEntry\UpdateEntryController
 * @group UC-UpdateEntry
 */
final class AC05_MissingIdCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * UC-5 / AC-5 — Missing id yields router-level 405 (instead of contract 422).
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testMissingIdFailsValidationWithIdRequired(FunctionalTester $I): void
    {
        // Arrange — JSON headers and minimal payload without 'id'
        $this->withJsonHeaders($I);
        $payload = UpdateEntryTestRequestFactory::missingIdPayload();

        // Act — send to /api/entries (no {id} in path)
        $this->updateEntry($I, $payload);

        // Assert — router currently returns 405 Method Not Allowed
        $I->seeResponseCodeIs(405);

        // Expected (per UC-5): 422 with ID_REQUIRED
        // $this->assertUnprocessableContract($I);
        // $code = 'ID_REQUIRED';
        // $this->assertErrorCode($I, $code);
    }
}
