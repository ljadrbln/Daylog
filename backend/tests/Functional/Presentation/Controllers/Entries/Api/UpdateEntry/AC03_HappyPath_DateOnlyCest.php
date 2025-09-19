<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Scenarios\Entries\UpdateEntryScenario;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Domain\Services\UuidGenerator;

/**
 * AC-03 (happy path — date): Update only the date and refresh updatedAt.
 *
 * Purpose:
 *   Verify the API endpoint PUT /api/entries/{id} updates only the provided 'date'
 *   and refreshes 'updatedAt' while keeping 'createdAt' intact and other fields unchanged.
 *
 * Mechanics:
 *   - Seed a single entry row via EntriesSeeding using UpdateEntryScenario::ac03DateOnly();
 *   - Issue PUT /api/entries/{id} with a JSON body containing only the new date;
 *   - Assert HTTP 200 + JSON envelope {success:true} per project contract;
 *   - Assert response payload: date replaced, title/body unchanged, createdAt unchanged,
 *     updatedAt strictly greater than previous.
 *
 * @covers \Daylog\Configuration\Providers\Entries\UpdateEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @covers \Daylog\Presentation\Controllers\Entries\Api\UpdateEntry\UpdateEntryController
 * @group UC-UpdateEntry
 */
final class AC03_HappyPath_DateOnlyCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * UC-5 / AC-3 — Happy path updates date and refreshes updatedAt.
     *
     * Purpose:
     * Validate that updating only the date:
     * - preserves id, title, body, createdAt;
     * - changes date to the provided value;
     * - strictly increases updatedAt (monotonicity per BR-2).
     *
     * Mechanics:
     * - Seed DB with a known row set (baseline);
     * - Send PUT with JSON payload (date only);
     * - Assert HTTP contract (200 + JSON envelope);
     * - Compare fields and verify updatedAt is strictly greater.
     *
     * @return void
     */
    public function testHappyPathUpdatesDateAndRefreshesUpdatedAt(FunctionalTester $I): void
    {
        // Arrange — seed DB and prepare request
        $this->withJsonHeaders($I);

        $dataset  = UpdateEntryScenario::ac03DateOnly();
        $rows     = $dataset['rows'];
        $targetId = $dataset['targetId'];
        $newDate  = $dataset['newDate'];

        $payload = UpdateEntryTestRequestFactory::dateOnlyPayload($targetId, $newDate);

        EntriesSeeding::intoDb($rows);

        // Act
        $this->updateEntry($I, $payload);

        // Assert (HTTP + contract)
        $this->assertOkContract($I);

        // Assert (response contains a valid UUID id and expected fields)
        $raw     = $I->grabResponse();
        $decoded = json_decode($raw, true);

        /** @var array{id: string, title: string, body: string, date: string, createdAt: string, updatedAt: string} $after */
        $after  = $decoded['data'];
        $before = $rows[0];

        $returnedId  = $after['id'];
        $isValidUuid = UuidGenerator::isValid($returnedId);

        $I->assertTrue($isValidUuid);
        $I->assertSame($targetId, $returnedId);

        // Field equality / inequality checks
        $I->assertSame($before['id'],        $after['id']);
        $I->assertSame($before['title'],     $after['title']);
        $I->assertSame($before['body'],      $after['body']);
        $I->assertSame($payload['date'],     $after['date']);
        $I->assertSame($before['createdAt'], $after['createdAt']);

        // updatedAt must be strictly greater than before (ISO-8601 string compare is valid)
        /** @var string $afterUpdatedAt */
        $afterUpdatedAt  = $after['updatedAt'];

        /** @var string $beforeUpdatedAt */
        $beforeUpdatedAt = $before['updatedAt'];

        $isStrictlyGreater = strcmp($afterUpdatedAt, $beforeUpdatedAt) > 0;
        $I->assertTrue($isStrictlyGreater);
    }
}
