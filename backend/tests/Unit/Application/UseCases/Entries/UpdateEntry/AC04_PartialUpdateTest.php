<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Assertion\UpdateEntryTitleAndBodyAssertions;

/**
 * UC-5 / AC-04 — Partial update.
 *
 * Purpose:
 * Given a valid id and a subset of fields, only provided fields must change
 * while others remain intact. The updatedAt timestamp is refreshed per BR-2.
 *
 * Mechanics:
 * - Seed repository with a valid Entry from EntryTestData::getOne().
 * - Build request with {id, title, date} while omitting body entirely.
 * - Validator is expected to run exactly once (domain specifics tested elsewhere).
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC04_PartialUpdateTest extends BaseUpdateEntryUnitTest
{
    use UpdateEntryTitleAndBodyAssertions;

    /**
     * Validate that only provided fields (title+body) change; date remains intact.
     *
     * @return void
     */
    public function testPartialUpdateChangesOnlyProvidedFields(): void
    {
        // Arrange
        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        $repo = $this->makeRepo();
        $repo->save($entry);

        $id       = $entry->getId();
        $newTitle = 'Updated title';
        $newBody  = 'Updated body';

        $request   = UpdateEntryTestRequestFactory::titleAndBody($id, $newTitle, $newBody);
        $validator = $this->makeValidatorOk();

        // Act
        $useCase  = $this->makeUseCase($repo, $validator);
        $response = $useCase->execute($request);
        $actual   = $response->getEntry();

        // Assert
        $this->assertTitleAndBodyUpdated($entry, $actual, $newTitle, $newBody);
    }
}
