<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * AC-07 (not found): Valid but absent UUID returns ENTRY_NOT_FOUND.
 *
 * Purpose:
 *   Verify that the API endpoint PUT /api/entries/{id} returns a not-found error
 *   when the provided identifier is a syntactically valid UUID v4 but is absent in storage.
 *   Ensures domain validation passes (ID_REQUIRED/ID_INVALID are not triggered)
 *   and repository lookup governs the outcome.
 *
 * Mechanics:
 *   - Keep DB clean (no row with the tested id);
 *   - Use UpdateEntryTestRequestFactory::notFoundPayload() to produce a valid UUID + update field(s);
 *   - PUT /api/entries/{id} with JSON body;
 *   - Assert HTTP 404 contract and ENTRY_NOT_FOUND code in the error payload.
 *
 * @covers \Daylog\Configuration\Providers\Entries\UpdateEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @covers \Daylog\Presentation\Controllers\Entries\Api\UpdateEntry\UpdateEntryController
 * @group UC-UpdateEntry
 */
final class AC07_NotFoundCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * UC-5 / AC-7 — Absent UUID yields 404 with ENTRY_NOT_FOUND.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testNotFoundFailsWithEntryNotFound(FunctionalTester $I): void
    {
        // Arrange
        $this->withJsonHeaders($I);

        $payload = UpdateEntryTestRequestFactory::notFoundPayload();

        // Act
        $this->updateEntry($I, $payload);

        // Assert — 404 + ENTRY_NOT_FOUND per contract
        $this->assertNotFoundContract($I);

        $code = 'ENTRY_NOT_FOUND';
        $this->assertErrorCode($I, $code);
    }
}
