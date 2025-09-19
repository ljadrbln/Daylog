<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\BaseEntryApiFunctionalCest;

/**
 * BaseUpdateEntryFunctionalCest
 *
 * Purpose:
 * Thin layer over the shared base to expose a single, intention-revealing helper
 * for issuing PUT requests against the Entries API.
 *
 * Mechanics:
 * - Builds the canonical PUT URL for updating entries without hardcoded duplication;
 * - Delegates the HTTP call to Codeception's REST module through FunctionalTester.
 */
abstract class BaseUpdateEntryFunctionalCest extends BaseEntryApiFunctionalCest
{
    /**
     * Issue a PUT request to the Entries API.
     *
     * Purpose:
     *   Send a canonical PUT /api/entries/{id} call for UC-5 scenarios from functional tests.
     *
     * Mechanics:
     *   - Accepts a typed array-shape payload with fields allowed by UC-5
     *     (`title?`, `body?`, `date?`).
     *   - Interpolates the provided entry id into the canonical API URL.
     *   - Sends JSON payload directly to the API endpoint.
     *   - Delegates HTTP to Codeception's REST module via FunctionalTester.
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @param array{
     *   id:string, 
     *   title?:string,
     *   body?:string,
     *   date?:string
     * } $payload                 Transport payload with updated entry data.
     * @return void
     */
    protected function updateEntry(FunctionalTester $I, array $payload): void
    {
        $id  = $payload['id'];
        $url = '/api/entries/%s';
        $url = sprintf($url, $id);

        $I->sendPut($url, $payload);
    }
}
