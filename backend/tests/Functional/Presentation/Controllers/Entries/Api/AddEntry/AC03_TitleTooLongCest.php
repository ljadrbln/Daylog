<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\AddEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Factory\AddEntryTestRequestFactory;

/**
 * AC-03: Title too long → TITLE_TOO_LONG (API boundary).
 *
 * Purpose:
 *   Verify that the API rejects a title exceeding ENTRY-BR-1 limit (after trimming)
 *   and returns a standardized 422 validation envelope consistent with UC-1 rules.
 *
 * Mechanics:
 *   - Build a canonical over-limit JSON payload via AddEntryTestRequestFactory::titleTooLongPayload();
 *   - POST the payload to /api/entries using the base helper;
 *   - Assert 422 contract (success=false) and presence of TITLE_TOO_LONG in the flat errors list.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 *
 * @group UC-AddEntry
 */
final class AC03_TitleTooLongCest extends BaseAddEntryFunctionalCest
{
    /**
     * API rejects over-limit title and reports TITLE_TOO_LONG.
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @return void
     */
    public function testTitleTooLongIsRejectedWithTitleTooLong(FunctionalTester $I): void
    {
        // Arrange
        $this->withJsonHeaders($I);
        $payload = AddEntryTestRequestFactory::titleTooLongPayload();

        // Act
        $this->addEntry($I, $payload);

        // Assert — contract and specific error code
        $this->assertUnprocessableContract($I);

        $code = 'TITLE_TOO_LONG';
        $this->assertErrorCode($I, $code);
    }
}
