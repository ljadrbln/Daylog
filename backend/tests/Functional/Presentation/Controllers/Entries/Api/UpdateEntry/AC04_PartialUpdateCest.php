<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

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
        // Arrange
        $dataset = UpdateEntryDataset::ac04TitleAndBody();
        $this->seedFromDataset($I, $dataset);        

        // Act
        $this->updateEntryFromDataset($I, $dataset);

        // Assert (HTTP + contract)
        $this->assertOkContract($I);

        // Assert (response contains a valid UUID id and expected fields)
        $actualEntry   = $this->grabTypedDataEnvelope($I);
        $expectedEntry = $dataset['rows'][0];
        
        $payload     = $dataset['payload'];
        $targetId    = $payload['id'];
        $returnedId  = $actualEntry['id'];
        $isValidUuid = UuidGenerator::isValid($returnedId);

        $I->assertTrue($isValidUuid);
        $I->assertSame($targetId, $returnedId);

        // Field equality / inequality checks
        $I->assertSame($expectedEntry['id'],        $actualEntry['id']);
        $I->assertSame($payload['title'],    $actualEntry['title']);
        $I->assertSame($payload['body'],     $actualEntry['body']);
        $I->assertSame($expectedEntry['date'],      $actualEntry['date']);
        $I->assertSame($expectedEntry['createdAt'], $actualEntry['createdAt']);

        // updatedAt must be strictly greater than expectedEntry (ISO-8601 string compare is valid)
        /** @var string $actualEntryUpdatedAt */
        $actualEntryUpdatedAt  = $actualEntry['updatedAt'];

        /** @var string $expectedEntryUpdatedAt */
        $expectedEntryUpdatedAt = $expectedEntry['updatedAt'];

        $isStrictlyGreater = strcmp($actualEntryUpdatedAt, $expectedEntryUpdatedAt) > 0;
        $I->assertTrue($isStrictlyGreater);
    }
}