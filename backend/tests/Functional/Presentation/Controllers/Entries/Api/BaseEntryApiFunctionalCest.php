<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api;

use Codeception\Util\HttpCode;
use Daylog\Configuration\Bootstrap\SqlFactory;
use Daylog\Tests\Support\Fixture\EntryFixture;
use Daylog\Tests\Support\Helper\EntriesSeeding;
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
 *   • assertBadRequestContract() — 400 + success=false, no data;
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
     * Assert common HTTP/JSON response contract.
     *
     * Purpose:
     *   Provide a single assertion path for success/error responses to remove duplication.
     *   Verifies: HTTP status, JSON content type, `success` flag, and presence/absence of "data".
     *
     * Mechanics:
     *   - Checks exact HTTP status;
     *   - Requires JSON response;
     *   - Asserts `success` equals expected;
     *   - Asserts `"data"` key presence according to scenario.
     *
     * @param FunctionalTester $I   Codeception actor for HTTP assertions.
     * @param int $expectedStatus   Expected HTTP status code (e.g., 200, 400, 404, 422).
     * @param bool $expectedSuccess Expected boolean value for the `success` field.
     * @param bool $expectData      Whether `"data"` must be present in the response body.
     * @return void
     */
    private function assertResponseContract(
        FunctionalTester $I,
        int $expectedStatus,
        bool $expectedSuccess,
        bool $expectData
    ): void {
        $I->seeResponseCodeIs($expectedStatus);
        $I->seeResponseIsJson();

        $successFlag = ['success' => $expectedSuccess];
        $I
            ->seeResponseContainsJson($successFlag);

        $needle = '"data"';
        if ($expectData === true) {
            $I->seeResponseContains($needle);
        } else {
            $I->dontSeeResponseContains($needle);
        }
    }

    /**
     * Assert 200 OK + success=true + data present.
     *
     * @param FunctionalTester $I
     * @return void
     */
    protected function assertOkContract(FunctionalTester $I): void
    {
        $status         = HttpCode::OK;
        $success        = true;
        $dataIsPresent  = true;

        $this->assertResponseContract($I, $status, $success, $dataIsPresent);
    }

    /**
     * Assert 400 Bad Request + standard error contract (no data).
     *
     * @param FunctionalTester $I
     * @return void
     */
    protected function assertBadRequestContract(FunctionalTester $I): void
    {
        $status         = HttpCode::BAD_REQUEST;
        $success        = false;
        $dataIsPresent  = false;

        $this->assertResponseContract($I, $status, $success, $dataIsPresent);
    }

    /**
     * Assert 404 Not Found + standard error contract (no data).
     *
     * @param FunctionalTester $I
     * @return void
     */
    protected function assertNotFoundContract(FunctionalTester $I): void
    {
        $status         = HttpCode::NOT_FOUND;
        $success        = false;
        $dataIsPresent  = false;

        $this->assertResponseContract($I, $status, $success, $dataIsPresent);
    }

    /**
     * Assert 422 Unprocessable Entity + standard error contract (no data).
     *
     * @param FunctionalTester $I
     * @return void
     */
    protected function assertUnprocessableContract(FunctionalTester $I): void
    {
        $status         = HttpCode::UNPROCESSABLE_ENTITY;
        $success        = false;
        $dataIsPresent  = false;

        $this->assertResponseContract($I, $status, $success, $dataIsPresent);
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

    /**
     * Seed DB with rows from a dataset (UC-specific builder).
     *
     * @param FunctionalTester $I Codeception tester (unused but kept for symmetry).
     * @param array{
     *     rows: array<int, array{
     *         id: string,
     *         title: string,
     *         body: string,
     *         date: string,
     *         createdAt: string,
     *         updatedAt: string
     *     }>
     * } $dataset Deterministic dataset containing prepared DB rows.
     *
     * @return void
     */
    protected function seedFromDataset(FunctionalTester $I, array $dataset): void
    {
        $rows = $dataset['rows'];
        EntriesSeeding::intoDb($rows);
    }
    
    /**
     * Grab "data" from the standard JSON envelope and narrow static types.
     *
     * Purpose:
     * Centralize JSON decoding + envelope validation,
     * returning the typed "data" section for further assertions.
     *
     * Mechanics:
     * - Uses JSON_THROW_ON_ERROR;
     * - Asserts envelope + "data" presence;
     * - Narrows to the provided @return shape to satisfy PHPStan.
     *
     * @param FunctionalTester $I
     * @return array{id: string, title: string, body: string, date: string, createdAt: string, updatedAt: string}
     */
    protected function grabTypedDataEnvelope(FunctionalTester $I): array
    {
        $raw = $I->grabResponse();
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

        $I->assertIsArray($decoded);
        $I->assertArrayHasKey('data', $decoded);

        /** @var array{
         *   success: bool,
         *   status?: int,
         *   code?: string,
         *   data: array{id: string, title: string, body: string, date: string, createdAt: string, updatedAt: string}
         * } $envelope
         */
        $envelope = $decoded;

        /** @var array{id: string, title: string, body: string, date: string, createdAt: string, updatedAt: string} $data */
        $data = $envelope['data'];

        return $data;
    }
}
