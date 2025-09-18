<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api;

use Codeception\Util\HttpCode;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\FunctionalTester;

/**
 * Base Cest for Entry API functional (HTTP) tests (shared for UC-3 GetEntry and UC-4 DeleteEntry).
 *
 * Purpose:
 * Provide a single reusable scaffold for black-box API checks of
 * GET/DELETE /api/entries/{id}: headers setup, DB cleanup/seeding hooks,
 * HTTP helpers (GET/DELETE), and standard contract assertions (200/404/422).
 *
 * Mechanics:
 * - Before/after each test: reset the `entries` table using EntryFixture (real DB wiring);
 * - Unify JSON headers (Accept/Content-Type) for all API calls;
 * - Offer small HTTP helpers: getEntry(), deleteEntry();
 * - Offer assertion helpers:
 *   • assertOkContract() — 200 + success=true + data present;
 *   • assertNotFoundContract() — 404 + success=false, no data;
 *   • assertUnprocessableContract() — 422 + success=false, no data;
 *   • assertErrorCode($code) — code equals a specific semantic identifier;
 *   • assertNoCodeOnSuccess() — optional guard for endpoints that omit "code" on success.
 *
 * Notes:
 * Real wiring is used end-to-end (web → controller → use case → repository).
 */
abstract class BaseEntryApiFunctionalCest
{
    /**
     * Clean table before each test.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function _before(FunctionalTester $I): void
    {
        $db = SqlFactory::get();
        EntryFixture::setDb($db);
        EntryFixture::cleanTable();
    }

    /**
     * Clean table after each test.
     *
     * @param FunctionalTester $I
     * @return void
     */
    public function _after(FunctionalTester $I): void
    {
        EntryFixture::cleanTable();
    }

    /**
     * Set standard JSON headers for API requests.
     *
     * @param FunctionalTester $I
     * @return void
     */
    protected function withJsonHeaders(FunctionalTester $I): void
    {
        $accept = 'application/json';
        $I->haveHttpHeader('Accept', $accept);

        $contentType = 'application/json';
        $I->haveHttpHeader('Content-Type', $contentType);
    }

    /**
     * Assert 200 OK + success=true + data present.
     *
     * @param FunctionalTester $I
     * @return void
     */
    protected function assertOkContract(FunctionalTester $I): void
    {
        $expected = HttpCode::OK;
        $I->seeResponseCodeIs($expected);
        $I->seeResponseIsJson();

        $success = ['success' => true];
        $I->seeResponseContainsJson($success);

        $needle = '"data"';
        $I->seeResponseContains($needle);
    }

    /**
     * Assert 404 Not Found + standard error contract (no data).
     *
     * @param FunctionalTester $I
     * @return void
     */
    protected function assertNotFoundContract(FunctionalTester $I): void
    {
        $expected = HttpCode::NOT_FOUND;
        $I->seeResponseCodeIs($expected);
        $I->seeResponseIsJson();

        $contract = ['success' => false];
        $I->seeResponseContainsJson($contract);

        $needle = '"data"';
        $I->dontSeeResponseContains($needle);
    }

    /**
     * Assert 422 Unprocessable Entity + standard error contract (no data).
     *
     * @param FunctionalTester $I
     * @return void
     */
    protected function assertUnprocessableContract(FunctionalTester $I): void
    {
        $expected = HttpCode::UNPROCESSABLE_ENTITY;
        $I->seeResponseCodeIs($expected);
        $I->seeResponseIsJson();

        $contract = ['success' => false];
        $I->seeResponseContainsJson($contract);

        $needle = '"data"';
        $I->dontSeeResponseContains($needle);
    }

    /**
     * Assert a single semantic response code is present (e.g., ENTRY_NOT_FOUND).
     *
     * @param FunctionalTester $I
     * @param string $code
     * @return void
     */
    protected function assertErrorCode(FunctionalTester $I, string $code): void
    {
        $errorCode = ['code' => $code];
        $I->seeResponseContainsJson($errorCode);
    }

    /**
     * Assert that successful response omits the "code" field (for endpoints without code=OK).
     *
     * @param FunctionalTester $I
     * @return void
     */
    protected function assertNoCodeOnSuccess(FunctionalTester $I): void
    {
        $needle = '"code"';
        $I->dontSeeResponseContains($needle);
    }
}
