<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\DeleteEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Factory\DeleteEntryTestRequestFactory;

/**
 * UC-4 / AC-01 — Happy path — Unit.
 *
 * Purpose:
 * Verify that DeleteEntry validates the request, deletes the entry by id from repository,
 * and returns a response DTO echoing the same id (valid UUID v4).
 *
 * Mechanics:
 * - Seed FakeEntryRepository with a valid Entry built from fixture.
 * - Build request DTO with the same id via factory helper.
 * - Validator is expected to run exactly once and succeed.
 * - Execute the use case; assert the response echoes id and the entity is absent in repo.
 *
 * @covers \Daylog\Application\UseCases\Entries\DeleteEntry\DeleteEntry::execute
 * @group UC-DeleteEntry
 */
final class AC01_HappyPathTest extends BaseDeleteEntryUnitTest
{
    /**
     * Validate happy path behavior: entry is deleted and response carries the same UUID.
     *
     * @return void
     */
    public function testHappyPathDeletesEntryAndReturnsResponseDto(): void
    {
        // Arrange
        $data     = EntryTestData::getOne();
        $entryId  = $data['id'];
        $expected = Entry::fromArray($data);

        $validator = $this->makeValidatorOk();
        $request   = DeleteEntryTestRequestFactory::happy($entryId);
        $repo      = $this->makeRepo();
        $repo->save($expected);

        // Act
        $useCase  = $this->makeUseCase($repo, $validator);
        $response = $useCase->execute($request);
        $actual   = $response->getEntry();
        $actualId = $actual->getId();

        // Assert: response echoes the same id
        $this->assertSame($entryId, $actualId);

        // Assert: entity is removed from storage
        $foundAfter = $repo->findById($entryId);
        $this->assertNull($foundAfter);
    }
}
