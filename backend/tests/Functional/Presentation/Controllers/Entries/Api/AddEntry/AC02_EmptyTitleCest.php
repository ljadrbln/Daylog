<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\AddEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Factory\AddEntryTestRequestFactory;

/**
 * AC-02: Empty title → TITLE_REQUIRED.
 *
 * Purpose:
 *   Ensure that an empty (after trimming) title is rejected at the API boundary
 *   and reported as a validation error consistent with UC-1 and ENTRY-BR-1.
 *
 * Mechanics:
 *   - Build a canonical invalid payload via AddEntryTestRequestFactory::emptyTitlePayload();
 *   - POST JSON to /api/entries using the base helper;
 *   - Assert JSON error envelope (success=false) and presence of TITLE_REQUIRED in errors list.
 *
 * @covers \Daylog\Configuration\Providers\Entries\AddEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 *
 * @group UC-AddEntry
 */
final class AC02_EmptyTitleCest extends BaseAddEntryFunctionalCest
{
    /**
     * API rejects whitespace-only title and returns TITLE_REQUIRED.
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @return void
     */
    public function testEmptyTitleIsRejectedWithTitleRequired(FunctionalTester $I): void
    {
        // Arrange
        /** @var array{title:string,body:string,date:string} $payload */
        $payload = AddEntryTestRequestFactory::emptyTitlePayload();

        // Act
        $this->addEntry($I, $payload);

        // Assert — envelope & code
        $I->seeResponseIsJson();

        // If your project standard uses 422 for validation errors, keep this:
        $I->seeResponseCodeIs(422);
        // If you standardize on 400 instead, switch to:
        // $I->seeResponseCodeIs(400);

        $I->seeResponseContainsJson(['success' => false]);

        // Check error code list contains TITLE_REQUIRED
        $raw      = $I->grabResponse();
        $decoded  = json_decode($raw, true);

        $I->assertIsArray($decoded, 'Response must be a JSON object.');
        $errors = $decoded['errors'] ?? [];
        $I->assertIsArray($errors, 'Response must contain an errors array.');
        $I->assertContains('TITLE_REQUIRED', $errors, 'TITLE_REQUIRED must be reported.');
    }
}
