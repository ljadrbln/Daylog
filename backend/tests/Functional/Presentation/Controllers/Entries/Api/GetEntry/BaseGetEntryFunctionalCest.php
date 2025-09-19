<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\GetEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\BaseEntryApiFunctionalCest;

/**
 * BaseGetEntryFunctionalCest
 *
 * Purpose:
 * Thin layer over the shared base to expose a single, intention-revealing helper
 * for issuing GET requests against the Entries API.
 *
 * Mechanics:
 * - Builds the canonical GET URL without hardcoded duplication;
 * - Delegates the HTTP call to Codeception's REST module through FunctionalTester.
 */
abstract class BaseGetEntryFunctionalCest extends BaseEntryApiFunctionalCest
{
    /**
     * Issue a GET request to the Entries API.
     *
     * Purpose:
     *   Send a canonical GET /api/entries/{id} call for UC-3 scenarios from functional tests.
     *
     * Mechanics:
     *   - Accepts a typed array-shape payload with a single 'id' (UUID).
     *   - Builds the route using a formatted pattern to avoid hardcoded concatenation.
     *   - Delegates HTTP to Codeception's REST module via FunctionalTester.
     *
     * @param FunctionalTester      $I       Codeception functional tester.
     * @param array{id:string}      $payload Transport payload with the entry UUID (e.g., ['id' => '...']).
     * @return void
     */
    protected function getEntry(FunctionalTester $I, array $payload): void
    {
        $id  = $payload['id'];
        $url = '/api/entries/%s';
        $url = sprintf($url, $id);

        $I->sendGet($url);
    }
}
