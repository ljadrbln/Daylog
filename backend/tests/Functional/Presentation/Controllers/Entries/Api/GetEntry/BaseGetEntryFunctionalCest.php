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
     * Send GET /api/entries/{id}.
     *
     * @param FunctionalTester $I
     * @param array $payload
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
