<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\GetEntry;

use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\BaseEntryApiFunctionalCest;

/**
 * BaseGetEntryFunctionalCest
 *
 * Purpose:
 * Provide a thin, intention-revealing helper for issuing canonical
 * GET /api/entries/{id} requests in UC-3 functional scenarios.
 *
 * Mechanics:
 * - Accepts a unified dataset shape { rows, payload, request } to keep tests DRY;
 * - Extracts 'id' from payload and builds the URL via a formatted pattern;
 * - Delegates the HTTP call to Codeception's REST module through FunctionalTester.
 */
abstract class BaseGetEntryFunctionalCest extends BaseEntryApiFunctionalCest
{
    /**
     * Issue a GET request to the Entries API using a prepared dataset.
     *
     * Purpose:
     *   Send a canonical GET /api/entries/{id} call for UC-3 scenarios from functional tests.
     *
     * Mechanics:
     *   - Accepts a typed dataset with 'payload' holding a single 'id' (UUID v4);
     *   - Uses a dedicated pattern variable to avoid hardcoded concatenation;
     *   - Invokes FunctionalTester->sendGet() with the fully formatted URL.
     *
     * @param FunctionalTester      $I       Codeception functional tester.
     * @param array{
     *   rows: array<int,array<string,string>>,
     *   payload: array{id:string},
     *   request: GetEntryRequestInterface
     * } $dataset
     *
     * @return void
     */
    protected function getEntryFromDataset(FunctionalTester $I, array $dataset): void
    {
        $payload = $dataset['payload'];
        $id      = $payload['id'];

        $pattern = '/api/entries/%s';
        $url     = sprintf($pattern, $id);

        $this->withJsonHeaders($I);
        $I->sendGet($url);
    }
}
