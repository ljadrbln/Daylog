<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\AddEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\BaseEntryApiFunctionalCest;

/**
 * BaseAddEntryFunctionalCest
 *
 * Purpose:
 * Thin layer over the shared base to expose a single, intention-revealing helper
 * for issuing POST requests against the Entries API.
 *
 * Mechanics:
 * - Builds the canonical POST URL without hardcoded duplication;
 * - Delegates the HTTP call to Codeception's REST module through FunctionalTester.
 */
abstract class BaseAddEntryFunctionalCest extends BaseEntryApiFunctionalCest
{
    /**
     * Issue a POST request to the Entries API.
     *
     * Purpose:
     *   Send a canonical POST /api/entries call for UC-1 scenarios from functional tests.
     *
     * Mechanics:
     *   - Accepts a typed array-shape payload with fields required by UC-1
     *     (`title`, `body`, `date`).
     *   - Sends JSON payload directly to the API endpoint.
     *   - Delegates HTTP to Codeception's REST module via FunctionalTester.
     *
     * @param FunctionalTester                   $I       Codeception functional tester.
     * @param array{title:string,body:string,date:string} $payload Transport payload with entry data.
     * @return void
     */
    protected function addEntry(FunctionalTester $I, array $payload): void
    {
        $url = '/api/entries';

        $I->sendPost($url, $payload);
    }
}
