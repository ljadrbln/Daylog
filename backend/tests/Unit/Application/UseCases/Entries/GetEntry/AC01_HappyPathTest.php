<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\GetEntry;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Factory\GetEntryTestRequestFactory;

/**
 * UC-3 / AC-01 — Happy path — Unit.
 *
 * Purpose:
 * Verify that GetEntry validates the request, loads the entry by id from repository,
 * and returns a response DTO carrying the same id with a valid UUID v4 format.
 *
 * Mechanics:
 * - Seed FakeEntryRepository with a valid Entry built from fixture.
 * - Build request DTO with the same id.
 * - Validator is expected to run exactly once and succeed.
 * - Execute the use case and assert that the returned entry id matches and is a valid UUID.
 *
 * @covers \Daylog\Application\UseCases\Entries\GetEntry\GetEntry::execute
 * @group UC-GetEntry
 */
final class AC01_HappyPathTest extends BaseGetEntryUnitTest
{
    /**
     * Validate happy path behavior and response DTO integrity.
     *
     * @return void
     */
    public function testHappyPathReturnsEntryById(): void
    {
        // Arrange
        $data = EntryTestData::getOne();
        $entryId  = $data['id'];
        $expected = Entry::fromArray($data);

        $validator = $this->makeValidatorOk();
        $request   = GetEntryTestRequestFactory::happy($entryId);
        $repo      = $this->makeRepo();
        $repo->save($expected);

        // Act
        $useCase  = $this->makeUseCase($repo, $validator);
        $response = $useCase->execute($request);
        $actual   = $response->getEntry();

        // Assert
        $areEqual = $expected->equals($actual);
        $this->assertTrue($areEqual);
    }
}
