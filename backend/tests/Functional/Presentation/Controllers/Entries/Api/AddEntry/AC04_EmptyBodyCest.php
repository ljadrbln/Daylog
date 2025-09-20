<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\AddEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Factory\AddEntryTestRequestFactory;

/**
 * AC-04: Empty body → BODY_REQUIRED (API boundary).
 *
 * Purpose:
 *   Verify that the API rejects an empty (post-trim) body and responds with the
 *   standardized 422 validation envelope consistent with UC-1 and ENTRY-BR rules.
 *
 * Mechanics:
 *   - Build a canonical invalid JSON payload via AddEntryTestRequestFactory::emptyBodyPayload();
 *   - POST payload to /api/entries using the shared base helper;
 *   - Assert 422 contract (success=false) and presence of BODY_REQUIRED in the flat errors list.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 *
 * @group UC-AddEntry
 */
final class AC04_EmptyBodyCest extends BaseAddEntryFunctionalCest
{
    /**
     * API rejects empty body and reports BODY_REQUIRED.
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @return void
     */
    public function testEmptyBodyIsRejectedWithBodyRequired(FunctionalTester $I): void
    {
        // Arrange
        $this->withJsonHeaders($I);
        $payload = AddEntryTestRequestFactory::emptyBodyPayload();

        // Act
        $this->addEntry($I, $payload);

        // Assert — contract and specific error code
        $this->assertUnprocessableContract($I);

        $code = 'BODY_REQUIRED';
        $this->assertErrorCode($I, $code);
    }
}
