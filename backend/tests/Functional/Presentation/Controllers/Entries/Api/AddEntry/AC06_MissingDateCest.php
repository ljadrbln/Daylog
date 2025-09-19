<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\AddEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Factory\AddEntryTestRequestFactory;

/**
 * AC-06: Missing date → DATE_REQUIRED (API boundary).
 *
 * Purpose:
 *   Verify that the API rejects a payload without the 'date' field and
 *   returns the standardized 422 validation envelope consistent with UC-1 rules.
 *
 * Mechanics:
 *   - Build a canonical JSON payload without 'date' via AddEntryTestRequestFactory::missingDatePayload();
 *   - POST the payload to /api/entries using the shared base helper;
 *   - Assert 422 contract (success=false) and presence of DATE_REQUIRED in the flat errors list.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 *
 * @group UC-AddEntry
 */
final class AC06_MissingDateCest extends BaseAddEntryFunctionalCest
{
    /**
     * API rejects payload without 'date' and reports DATE_REQUIRED.
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @return void
     */
    public function testMissingDateIsRejectedWithDateRequired(FunctionalTester $I): void
    {
        // Arrange
        $payload = AddEntryTestRequestFactory::missingDatePayload();

        // Act
        $this->addEntry($I, $payload);

        // Assert — contract and specific error code
        $this->assertUnprocessableContract($I);

        $code = 'DATE_REQUIRED';
        $this->assertErrorCode($I, $code);
    }
}
