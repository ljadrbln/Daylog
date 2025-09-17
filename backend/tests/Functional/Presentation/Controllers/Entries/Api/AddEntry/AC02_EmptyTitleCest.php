<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\AddEntry;

use Codeception\Util\HttpCode;
use Daylog\Tests\FunctionalTester;

/**
 * UC-1 / AC-02 — Empty title — Functional.
 *
 * Purpose:
 *   Ensure the API endpoint POST /api/entries rejects effectively empty titles
 *   (after trimming) and returns a validation response with TITLE_REQUIRED.
 *
 * Mechanics:
 *   - Send JSON payload where title is whitespace only.
 *   - Expect HTTP 422 Unprocessable Entity.
 *   - Assert response JSON has success=false and errors contains TITLE_REQUIRED.
 *   - Assert no 'data' key is present on error (per project response contract).
 *
 * Notes:
 *   This is a black-box test via HTTP (PhpBrowser). Repository invariants are
 *   validated indirectly via status and error contract rather than direct interaction checks.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Api\AddEntryController::store
 * @group UC-AddEntry
 */
final class AC02_EmptyTitleCest
{
    /**
     * Given whitespace-only title, POST /api/entries returns 422 with TITLE_REQUIRED.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function emptyTitleReturns422AndTitleRequired(FunctionalTester $I): void
    {
        $url         = '/api/entries';
        $accept      = 'application/json';
        $contentType = 'application/json';

        $I->haveHttpHeader('Accept', $accept);
        $I->haveHttpHeader('Content-Type', $contentType);

        $payload = [
            'title' => '   ',
            'body'  => 'Valid body',
            'date'  => '2025-09-17'
        ];

        $raw = json_encode($payload);

        $I->sendRawPOST($url, $raw);

        $expected = HttpCode::UNPROCESSABLE_ENTITY; // 422
        $I->seeResponseCodeIs($expected);
        $I->seeResponseIsJson();

        // Contract checks
        $I->seeResponseContainsJson(['success' => false]);
        $I->seeResponseContainsJson(['errors' => ['TITLE_REQUIRED']]);

        // Ensure no 'data' key on error responses
        $I->dontSeeResponseContains('"data"');
    }
}