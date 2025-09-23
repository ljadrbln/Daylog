<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\ListEntries;

use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;
use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\BaseEntryApiFunctionalCest;

/**
 * BaseListEntriesFunctionalCest
 *
 * Purpose:
 * Thin layer over the shared base to expose a single, intention-revealing helper
 * for issuing GET requests against the Entries API (UC-2).
 *
 * Mechanics:
 * - Builds the canonical GET URL (`/api/entries`) with query params from dataset payload;
 * - Delegates the HTTP call to Codeception's REST module through FunctionalTester;
 * - Keeps tests DRY and intention-revealing.
 */
abstract class BaseListEntriesFunctionalCest extends BaseEntryApiFunctionalCest
{
    /**
     * Issue a GET request to the Entries API.
     *
     * Purpose:
     *   Send a canonical GET /api/entries call for UC-2 scenarios from functional tests.
     *
     * Mechanics:
     *   - Accepts a dataset with payload built for UC-2 (`page`, `perPage`, `date`, `dateFrom`,
     *     `dateTo`, `query`, `sortField`, `sortDir`);
     *   - Sends these as query parameters to the API endpoint;
     *   - Delegates HTTP to Codeception's REST module via FunctionalTester.
     *
     * @param FunctionalTester $I
     * @param array{
     *   payload: array{
     *     page?: int,
     *     perPage?: int,
     *     date?: string,
     *     dateFrom?: string,
     *     dateTo?: string,
     *     query?: string,
     *     sortField?: string,
     *     sortDir?: string
     *   },
     *   request: ListEntriesRequestInterface
     * } $dataset
     * @return void
     */
    protected function listEntriesFromDataset(FunctionalTester $I, array $dataset): void
    {
        $url     = '/api/entries';
        $payload = $dataset['payload'];

        $this->withJsonHeaders($I);
        $I->sendGet($url, $payload);
    }
}
