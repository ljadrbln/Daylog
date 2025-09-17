<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Page;

use Codeception\Util\HttpCode;
use Daylog\Tests\FunctionalTester;

/**
 * Functional test for EntriesListPageController.
 *
 * Purpose:
 *   Ensure that the entries list page (UI) is reachable via GET /entries.
 *
 * Mechanics:
 *   - Send a GET request to /entries.
 *   - Expect HTTP 200 response code.
 *   - Content body may be empty at this stage (echo '').
 *
 * Future:
 *   Later assertions may include template rendering, meta tags, and entry list HTML.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Page\EntriesListPageController::show
 */
final class EntriesListPageCest
{
    /**
     * Verify that the entries list page is reachable and returns 200 OK.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function showEntriesListPage(FunctionalTester $I): void
    {
        $url = '/entries';
        $I->amOnPage($url);

        $expected = HttpCode::OK;
        $I->seeResponseCodeIs($expected);
    }
}
