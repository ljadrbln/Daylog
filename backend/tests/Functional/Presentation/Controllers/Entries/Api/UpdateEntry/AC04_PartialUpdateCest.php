<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Scenarios\Entries\UpdateEntryScenario;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Domain\Services\UuidGenerator;

/**
 * AC-04 (partial update): Update only provided fields (title + body), keep others intact.
 *
 * Purpose:
 *   Verify the API endpoint PUT /api/entries/{id} performs a selective merge:
 *   it updates exactly the provided fields ('title', 'body') and leaves 'date'
 *   and 'createdAt' unchanged, while strictly increasing 'updatedAt'.
 *
 * Mechanics:
 *   - Seed a single entry row via EntriesSeeding using UpdateEntryScenario::ac04TitleAndBody();
 *   - Issue PUT /api/entries/{id} with a JSON body containing title and body (date omitted);
 *   - Assert HTTP 200 + JSON envelope {success:true} per project contract;
 *   - Assert response payload: title/body replaced, date/createdAt unchanged,
 *     updatedAt strictly greater than previous.
 *
 * @covers \Daylog\Configuration\Providers\Entries\UpdateEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @covers \Daylog\Presentation\Controllers\Entries\Api\UpdateEntry\UpdateEntryController
 * @group UC-UpdateEntry
 */
final class AC04_PartialUpdateCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * UC-5 / AC-4 — Partial update changes only provided fields (title + body).
     *
     * Purpose:
     * Validate that updating title and body:
     * - preserves id, date, createdAt;
     * - changes title and body to the provided values;
     * - strictly increases updatedAt (monotonicity per BR-2).
     *
     * Mechanics:
     * - Seed DB with a known row set (baseline);
     * - Send PUT with JSON payload (title + body, date omitted);
     * - Assert HTTP contract (200 + JSON envelope);
     * - Compare fields and verify updatedAt is strictly greater.
     *
     * @return void
     */
    public function testPartialUpdateChangesOnlyProvidedFields(FunctionalTester $I): void
    {
        // Arrange — seed DB and prepare request
        $this->withJsonHeaders($I);

        $dataset  = UpdateEntryScenario::ac04TitleAndBody();
        $rows     = $dataset['rows'];
        $targetId = $dataset['targetId'];
        $newTitle = $dataset['newTitle'];
        $newBody  = $dataset['newBody'];

        $payload = UpdateEntryTestRequestFactory::titleAndBodyPayload($targetId, $newTitle, $newBody);

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
        $I->assertSame($newTitle,            $after['title']);
        $I->assertSame($newBody,             $after['body']);
        $I->assertSame($before['date'],      $after['date']);
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
