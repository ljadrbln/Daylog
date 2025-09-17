<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Page;

use Codeception\Util\HttpCode;
use Daylog\Tests\FunctionalTester;

/**
 * Functional test for EntryCreatePageController.
 *
 * Purpose:
 *   Ensure that the entry creation page (UI) is reachable via GET /entries/create.
 *
 * Mechanics:
 *   - Send a GET request to /entries/create.
 *   - Expect HTTP 200 response code.
 *
 * @covers \Daylog\Presentation\Controllers\Entries\Page\EntryCreatePageController::show
 * @group ui-page
 */
final class EntryCreatePageCest
{
    /**
     * Verify that the entry creation page is reachable and returns 200 OK.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function showEntryCreatePage(FunctionalTester $I): void
    {
        $url = '/entries/create';
        $I->amOnPage($url);

        $expected = HttpCode::OK;
        $I->seeResponseCodeIs($expected);
    }
}
