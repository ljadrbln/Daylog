<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\DeleteEntry;

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
     * Send DELETE request to /api/entries/{id}.
     *
     * @param FunctionalTester $I
     * @param string $id
     * @return void
     */
    protected function deleteEntry(FunctionalTester $I, string $id): void
    {
        $url = '/api/entries/%s';
        $url = sprintf($url, $id);

        $I->sendDelete($url);
    }
}
