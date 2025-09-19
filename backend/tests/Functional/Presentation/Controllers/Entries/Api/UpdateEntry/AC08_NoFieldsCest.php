<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;

/**
 * AC-08 (no fields to update): 422 with NO_FIELDS_TO_UPDATE.
 *
 * Purpose:
 *   Verify that the API endpoint PUT /api/entries/{id} rejects a request that contains
 *   only an id without any updatable fields (title/body/date) and returns the
 *   transport-level validation error code NO_FIELDS_TO_UPDATE.
 *
 * Mechanics:
 *   - Build a payload with only the id via UpdateEntryTestRequestFactory::idOnlyPayload();
 *   - Issue PUT /api/entries/{id} with an empty body of updates;
 *   - Assert HTTP 422 contract and NO_FIELDS_TO_UPDATE in the errors array.
 *
 * @covers \Daylog\Configuration\Providers\Entries\UpdateEntryProvider
 * @covers \Daylog\Presentation\Controllers\Entries\Api\UpdateEntry\UpdateEntryController
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC08_NoFieldsCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * UC-5 / AC-08 — Only id provided → 422 NO_FIELDS_TO_UPDATE.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testNoFieldsToUpdateFailsValidationWithNoFieldsToUpdate(FunctionalTester $I): void
    {
        // Arrange — JSON headers and payload with only id
        $this->withJsonHeaders($I);

        $payload = UpdateEntryTestRequestFactory::idOnlyPayload();

        // Act — PUT /api/entries/{id} where body has no updatable fields
        $this->updateEntry($I, $payload);

        // Assert — 422 + NO_FIELDS_TO_UPDATE per contract
        $this->assertUnprocessableContract($I);

        $code = 'NO_FIELDS_TO_UPDATE';
        $this->assertErrorCode($I, $code);
    }
}
