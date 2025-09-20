<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Assertion\UpdateEntryTitleAndBodyAssertions;
use Daylog\Tests\Support\Datasets\Entries\UpdateEntryDataset;

/**
 * AC-4 (partial update): Given a valid id and any subset of title, body, date,
 * when updating, then only provided fields change; others remain intact.
 *
 * Purpose:
 * Verify that UpdateEntry performs a selective merge: it updates exactly the
 * fields present in the request and leaves unspecified fields untouched.
 * Uses real wiring (Provider + SqlFactory) and a clean DB prepared by the base class.
 *
 * Mechanics:
 * - Seed a single row via EntriesSeeding::intoDb() from UpdateEntryScenario;
 * - Build a request with the subset (title+body), omit date;
 * - Execute the real use case; assert via shared trait that only provided
 *   fields changed and updatedAt increased.
 *
 * @covers \Daylog\Configuration\Providers\Entries\UpdateEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC04_PartialUpdateTest extends BaseUpdateEntryIntegrationTest
{
    use UpdateEntryTitleAndBodyAssertions;

    /**
     * AC-04: only provided fields (title+body) change; date remains intact.
     *
     * @return void
     */
    public function testPartialUpdateChangesOnlyProvidedFields(): void
    {
        // Arrange
        $dataset = UpdateEntryDataset::ac04TitleAndBody();
        $this->seedFromDataset($dataset);

        // Act
        $request  = $dataset['request'];
        $response = $this->useCase->execute($request);

        // Assert
        $newTitle = $dataset['payload']['title'];
        $newBody  = $dataset['payload']['body'];

        $expectedEntry = $dataset['rows'][0];
        $expectedEntry = Entry::fromArray($expectedEntry);
        $actualEntry   = $response->getEntry();

        $this->assertTitleAndBodyUpdated($expectedEntry, $actualEntry, $newTitle, $newBody);
    }
}