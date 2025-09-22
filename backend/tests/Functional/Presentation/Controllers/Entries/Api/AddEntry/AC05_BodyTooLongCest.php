<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\AddEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\AddEntryDataset;

/**
 * AC-05: Body too long → BODY_TOO_LONG (API boundary).
 *
 * Purpose:
 *   Verify that the API rejects a body exceeding ENTRY-BR-2 limit (after trimming)
 *   and returns the standardized 422 validation envelope consistent with UC-1 rules.
 *
 * Mechanics:
 *   - Build a canonical over-limit JSON payload via AddEntryTestRequestFactory::bodyTooLongPayload();
 *   - POST the payload to /api/entries using the base helper;
 *   - Assert 422 contract (success=false) and presence of BODY_TOO_LONG in the flat errors list.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 *
 * @group UC-AddEntry
 */
final class AC05_BodyTooLongCest extends BaseAddEntryFunctionalCest
{
    /**
     * API rejects over-limit body and reports BODY_TOO_LONG.
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @return void
     */
    public function testBodyTooLongIsRejectedWithBodyTooLong(FunctionalTester $I): void
    {
        // Arrange
        $dataset = AddEntryDataset::ac05TooLongBody();

        // Act
        $this->addEntryFromDataset($I, $dataset);

        // Assert
        $this->assertUnprocessableContract($I);

        $code = 'BODY_TOO_LONG';
        $this->assertErrorCode($I, $code);
    }
}
