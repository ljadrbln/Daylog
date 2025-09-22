<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * AC-02 (happy path — body): Update only the body and refresh updatedAt.
 *
 * Purpose:
 *   Verify the API endpoint PUT /api/entries/{id} updates only the provided 'body'
 *   and refreshes 'updatedAt' while keeping 'createdAt' intact and other fields unchanged.
 *
 * Mechanics:
 *   - Seed a single entry row via EntriesSeeding using UpdateEntryScenario::ac02BodyOnly();
 *   - Issue PUT /api/entries/{id} with a JSON body containing only the new body;
 *   - Assert HTTP 200 + JSON envelope {success:true} per project contract;
 *   - Assert response payload: body replaced, title/date unchanged, createdAt unchanged,
 *     updatedAt strictly greater than previous.
 *
 * @covers \Daylog\Configuration\Providers\Entries\UpdateEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @covers \Daylog\Presentation\Controllers\Entries\Api\UpdateEntry\UpdateEntryController
 * @group UC-UpdateEntry
 */
final class AC02_HappyPath_BodyOnlyCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * UC-5 / AC-2 — Happy path updates body and refreshes updatedAt.
     *
     * Purpose:
     * Validate that updating only the body:
     * - preserves id, title, date, createdAt;
     * - changes body to the provided value;
     * - strictly increases updatedAt (monotonicity per BR-2).
     *
     * Mechanics:
     * - Seed DB with a known row set (baseline);
     * - Send PUT with JSON payload (body only);
     * - Assert HTTP contract (200 + JSON envelope);
     * - Compare fields and verify updatedAt is strictly greater.
     *
     * @return void
     */
    public function testHappyPathUpdatesBodyAndRefreshesUpdatedAt(FunctionalTester $I): void
    {
        // Arrange
        $dataset = UpdateEntryDataset::ac02BodyOnly();
        $this->seedFromDataset($I, $dataset);        

        // Act
        $this->withJsonHeaders($I);
        $this->updateEntryFromDataset($I, $dataset);

        // Assert (HTTP + contract)
        $this->assertOkContract($I);

        // Assert (response contains a valid UUID id and expected fields)
        $expectedEntry = $dataset['rows'][0];
        $actualEntry   = $this->grabTypedDataEnvelope($I);
        
        $payload     = $dataset['payload'];
        $targetId    = $payload['id'];
        $returnedId  = $actualEntry['id'];
        $isValidUuid = UuidGenerator::isValid($returnedId);

        $I->assertTrue($isValidUuid);
        $I->assertSame($targetId, $returnedId);

        // Field equality / inequality checks
        $I->assertSame($expectedEntry['id'],         $actualEntry['id']);
        $I->assertSame($expectedEntry['title'],      $actualEntry['title']);
        $I->assertSame($payload['body'],             $actualEntry['body']);
        $I->assertSame($expectedEntry['date'],       $actualEntry['date']);
        $I->assertSame($expectedEntry['createdAt'],  $actualEntry['createdAt']);

        // updatedAt must be strictly greater than expectedEntry (ISO-8601 string compare is valid)
        /** @var string $actualEntryUpdatedAt */
        $actualEntryUpdatedAt  = $actualEntry['updatedAt'];

        /** @var string $expectedEntryUpdatedAt */
        $expectedEntryUpdatedAt = $expectedEntry['updatedAt'];

        $isStrictlyGreater = strcmp($actualEntryUpdatedAt, $expectedEntryUpdatedAt) > 0;
        $I->assertTrue($isStrictlyGreater);
    }
}