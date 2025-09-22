<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\DeleteEntry;

use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequestInterface;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\BaseEntryApiFunctionalCest;

/**
 * BaseDeleteEntryFunctionalCest
 *
 * Purpose:
 * Thin layer over the shared base to expose a single, intention-revealing helper
 * for issuing DELETE requests against the Entries API.
 *
 * Mechanics:
 * - Builds the canonical DELETE URL via utility (EntriesApi) to avoid hardcoded routes;
 * - Delegates the HTTP call to Codeception's REST module through FunctionalTester.
 */
abstract class BaseDeleteEntryFunctionalCest extends BaseEntryApiFunctionalCest
{
    /**
     * Issue a DELETE request to the Entries API.
     *
     * Purpose:
     *   Send a canonical DELETE /api/entries/{id} call for UC-4 scenarios from functional tests.
     *
     * Mechanics:
     *   - Accepts a typed array-shape payload with a single 'id' (UUID).
     *   - Builds the route using a formatted pattern to avoid hardcoded concatenation.
     *   - Delegates HTTP to Codeception's REST module via FunctionalTester.
     *
     * @param FunctionalTester      $I       Codeception functional tester.
     * @param array{
     *   rows: array<int,array<string,string>>,
     *   payload: array{id:string},
     *   request: DeleteEntryRequestInterface
     * } $dataset
     * 
     * @return void
     */
    protected function deleteEntryFromDataset(FunctionalTester $I, array $dataset): void
    {
        $payload = $dataset['payload'];
        $id      = $payload['id'];

        $url = '/api/entries/%s';
        $url = sprintf($url, $id);

        $I->sendDelete($url);
    }
}
