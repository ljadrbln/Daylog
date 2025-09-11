<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Factory\UpdateEntryTestRequestFactory;
use Daylog\Tests\Support\Assertion\UpdateEntryTitleOnlyAssertions;

/**
 * UC-5 / AC-01 — Happy path (title-only) — Unit.
 *
 * Purpose:
 * Verify that only the title changes, other fields are preserved,
 * and timestamps remain valid/monotonic in the response DTO.
 *
 * Mechanics:
 * - Seed fake repo with a valid Entry.
 * - Build request via UpdateEntryTestRequestFactory::titleOnly().
 * - Execute use case; assert via shared trait.
 *
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
 * @group UC-UpdateEntry
 */
final class AC01_HappyPath_TitleOnlyTest extends BaseUpdateEntryUnitTest
{
    use UpdateEntryTitleOnlyAssertions;

    /**
     * Validate title-only update behavior and response DTO integrity.
     *
     * @return void
     */
    public function testHappyPathUpdatesTitleOnlyAndReturnsResponseDto(): void
    {
        // Arrange
        $data = EntryTestData::getOne();
        $expectedEntry = Entry::fromArray($data);

        $repo = $this->makeRepo();
        $repo->save($expectedEntry);

        $id       = $expectedEntry->getId();
        $newTitle = 'Updated title';

        /** @var \Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface $request */
        $request = UpdateEntryTestRequestFactory::titleOnly($id, $newTitle);

        $validator = $this->makeValidatorOk();

        // Act
        $useCase  = $this->makeUseCase($repo, $validator);
        $response = $useCase->execute($request);
        $actualEntry = $response->getEntry();

        // Assert
        $this->assertTitleOnlyUpdated($expectedEntry, $actualEntry, $newTitle);
    }
}
