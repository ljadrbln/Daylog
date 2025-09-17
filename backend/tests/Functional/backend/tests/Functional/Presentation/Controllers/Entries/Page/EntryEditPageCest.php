<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Page;

use Codeception\Util\HttpCode;
use Daylog\Tests\FunctionalTester;

/**
 * Functional test for EntryEditPageController.
 *
 * Purpose:
 *   Ensure that the entry edit page (UI) is reachable via GET /entries/{id}/edit.
 *
 * Mechanics:
 *   - Use a syntactically valid UUID v4 in the route.
 *   - Send a GET request to /entries/{id}/edit.
 *   - Expect HTTP 200 response code (HTML shell).
 *
 * Notes:
 *   Later, when UC-3 GetEntry is integrated, tests will split into:
 *   - existing entry → 200
 *   - non-existing entry → 404
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Page\EntryEditPageController::show
 * @group ui-page
 */
final class EntryEditPageCest
{
    /**
     * Verify that the entry edit page is reachable with a valid UUID.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function showEntryEditPage(FunctionalTester $I): void
    {
        $id = '11111111-2222-4333-8444-555555555555';
        $url = '/entries/' . $id . '/edit';

        $I->amOnPage($url);

        $expected = HttpCode::OK;
        $I->seeResponseCodeIs($expected);
    }
}
