<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\GetEntry;

use Codeception\Util\HttpCode;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\FunctionalTester;

/**
 * Base Cest for UC-3 GetEntry functional (HTTP) tests.
 *
 * Purpose:
 *   Provide JSON headers setup, simple GET helpers, and DB seeding/cleanup
 *   for black-box API checks of GET /api/entries/{id}.
 *
 * Mechanics:
 *   - Ensure clean 'entries' table before/after test via fixtures;
 *   - Set JSON headers (Accept/Content-Type) for consistency across methods;
 *   - Offer small assertion helpers for 200/404/422 contracts.
 *
 * Notes:
 *   Real wiring is used end-to-end (web → controller → use case → repository).
 */
abstract class BaseGetEntryFunctionalCest
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
     * Send GET request to /api/entries/{id}.
     *
     * @param FunctionalTester $I
     * @param string $id
     * @return void
     */
    protected function getEntry(FunctionalTester $I, string $id): void
    {
        $base = '/api/entries';
        $format = '%s/%s';
        $url = sprintf($format, $base, $id);

        $I->sendGet($url);
    }

    /**
     * Assert 200 OK + success=true + data present, no errors key.
     *
     * @param FunctionalTester $I
     * @return void
     */
    protected function assertOkContract(FunctionalTester $I): void
    {
        $expected = HttpCode::OK;
        $I->seeResponseCodeIs($expected);
        $I->seeResponseIsJson();

        $ok = ['success' => true];
        $I->seeResponseContainsJson($ok);

        $needle = '"errors"';
        $I->dontSeeResponseContains($needle);
    }

    /**
     * Assert 404 Not Found + standard error contract.
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
     * Assert 422 Unprocessable Entity + standard error contract.
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
     * Assert a single validation/error code is present in 'errors'.
     *
     * @param FunctionalTester $I
     * @param string $code
     * @return void
     */
    protected function assertErrorCode(FunctionalTester $I, string $code): void
    {
        $errors = ['errors' => [$code]];
        $I->seeResponseContainsJson($errors);
    }

    /**
     * Seed deterministic rows into real DB.
     *
     * @param array<int,array<string,mixed>> $rows
     * @return void
     */
    protected function seedIntoDb(array $rows): void
    {
        EntriesSeeding::intoDb($rows);
    }
}
