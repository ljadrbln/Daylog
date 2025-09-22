<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;

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
     * Send PUT /api/entries/{id} using dataset contents.
     *
     * Purpose:
     * Reuse canonical datasets (rows + payload + request) directly in functional tests.
     * This wrapper extracts the `payload` from the dataset and issues the HTTP call
     * against the UpdateEntry API, avoiding duplication of request-building logic.
     *
     * Mechanics:
     * - Build URL via entry id from dataset (payload['id']);
     * - Send JSON payload with standard headers (already set in Arrange);
     * - Used in AC happy-path and error-path functional tests for UC-5.
     *
     * @param FunctionalTester $I
     * @param array{
     *   rows: array<int,array<string,string>>,
     *   payload: array{id:string,title?:string,body?:string,date?:string},
     *   request: UpdateEntryRequestInterface
     * } $dataset
     * @return void
     */
    protected function updateEntryFromDataset(FunctionalTester $I, array $dataset): void
    {
        $payload = $dataset['payload'];
        $id      = $payload['id'];

        $url = '/api/entries/%s';
        $url = sprintf($url, $id);

        $this->withJsonHeaders($I);
        $I->sendPut($url, $payload);
    }
}
