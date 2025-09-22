<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\AddEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\AddEntryDataset;

/**
 * AC-04: Missing body → BODY_REQUIRED (API boundary).
 *
 * Purpose:
 *   Verify that the API rejects a request without the body field and responds with the
 *   standardized 422 validation envelope consistent with UC-1 and ENTRY-BR rules.
 *
 * Mechanics:
 *   - Build a canonical invalid JSON payload via AddEntryDataset::ac04MissingBody();
 *   - POST payload to /api/entries using the shared base helper;
 *   - Assert 422 contract (success=false) and presence of BODY_REQUIRED in the flat errors list.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 *
 * @group UC-AddEntry
 */
final class AC04_MissingBodyCest extends BaseAddEntryFunctionalCest
{
    /**
     * API rejects missing body and reports BODY_REQUIRED.
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @return void
     */
    public function testMissingBodyIsRejectedWithBodyRequired(FunctionalTester $I): void
    {
        // Arrange
        $dataset = AddEntryDataset::ac04MissingBody();

        // Act
        $this->addEntryFromDataset($I, $dataset);

        // Assert
        $this->assertBadRequestContract($I);

        $code = 'BODY_REQUIRED';
        $this->assertErrorCode($I, $code);
    }
}
