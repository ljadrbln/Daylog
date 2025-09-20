<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * AC-09 (empty title): 422 with TITLE_REQUIRED.
 *
 * Purpose:
 *   Verify that PUT /api/entries/{id} rejects an explicitly provided empty title
 *   (after trimming) and returns a transport-level validation error with code TITLE_REQUIRED.
 *
 * Mechanics:
 *   - Build a payload with a valid UUID id and a whitespace-only title via factory;
 *   - Issue PUT /api/entries/{id} with JSON body;
 *   - Assert HTTP 422 contract and TITLE_REQUIRED in the error payload.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\UpdateEntry\UpdateEntryController
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC09_EmptyTitleCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * UC-5 / AC-09 — Provided empty (after trimming) title → 422 TITLE_REQUIRED.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function testEmptyTitleFailsValidationWithTitleRequired(FunctionalTester $I): void
    {
        // Arrange
        $dataset = UpdateEntryDataset::ac09EmptyTitle();

        // Act
        $this->withJsonHeaders($I);
        $this->updateEntryFromDataset($I, $dataset);

        // Assert
        $this->assertUnprocessableContract($I);

        $code = 'TITLE_REQUIRED';
        $this->assertErrorCode($I, $code);
    }
}
