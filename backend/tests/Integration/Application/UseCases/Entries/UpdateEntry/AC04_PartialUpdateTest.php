<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Scenarios\Entries\UpdateEntryScenario;
use Daylog\Tests\Support\Assertion\UpdateEntryTitleAndBodyAssertions;

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
        $dataset  = UpdateEntryScenario::ac04TitleAndBody();

        $rows     = $dataset['rows'];
        $targetId = $dataset['targetId'];
        $newTitle = $dataset['newTitle'];
        $newBody  = $dataset['newBody'];
        
        $request = UpdateEntryTestRequestFactory::titleAndBody($targetId, $newTitle, $newBody);
        EntriesSeeding::intoDb($rows);

        // Act
        $response = $this->useCase->execute($request);
        $actual   = $response->getEntry();

        // Assert
        $expected = Entry::fromArray($rows[0]);
        $this->assertTitleAndBodyUpdated($expected, $actual, $newTitle, $newBody);
    }
}
