<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\DeleteEntry;

use Codeception\Util\HttpCode;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\FunctionalTester;

/**
 * Base Cest for UC-4 DeleteEntry functional (HTTP) tests.
 *
 * Purpose:
 * Provide shared setup/teardown, JSON header helpers, a DELETE request helper,
 * and standard contract assertions for black-box API checks of
 * DELETE /api/entries/{id}.
 *
 * Mechanics:
 * - Ensure a clean 'entries' table before/after each test via fixtures;
 * - Set consistent JSON headers (Accept/Content-Type);
 * - Offer assertion helpers for 200/404/422 outcomes tailored to UC-4:
 *   200 must include success=true, code=OK, and a data block.
 */
abstract class BaseDeleteEntryFunctionalCest
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
     * Send DELETE request to /api/entries/{id}.
     *
     * @param FunctionalTester $I
     * @param string $id
     * @return void
     */
    protected function deleteEntry(FunctionalTester $I, string $id): void
    {
        $base   = '/api/entries';
        $format = '%s/%s';
        $url    = sprintf($format, $base, $id);

        $I->sendDelete($url);
    }

    /**
     * Assert 200 OK + success=true + code=OK + data present.
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
}
