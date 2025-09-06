<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries;

use Codeception\Test\Unit;

use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequest;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequestInterface;

use Daylog\Application\Validators\Entries\DeleteEntry\DeleteEntryValidatorInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\UseCases\Entries\DeleteEntry\DeleteEntry;

use Daylog\Domain\Services\UuidGenerator;
use Daylog\Domain\Models\Entries\Entry;

use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Fakes\FakeEntryRepository;

/**
 * Unit tests for UC-4 DeleteEntry.
 *
 * Purpose:
 * Verify that DeleteEntry validates input, deletes an existing entry, and returns a confirmation DTO.
 * Ensure repository is not touched when validator throws (transport/domain violations are handled upstream).
 *
 * Mechanics:
 * - Uses FakeEntryRepository for deterministic, in-memory storage.
 * - Uses a mocked validator; actual validation logic is covered elsewhere.
 * - Data source comes from EntryTestData::getOne().
 *
 * @covers \Daylog\Application\UseCases\Entries\DeleteEntry\DeleteEntry
 * @group UC-DeleteEntry
 */
final class DeleteEntryTest extends Unit
{
    /**
     * Happy path: validates request, deletes the entry, and returns a response DTO with the same id.
     *
     * Mechanics:
     * - Arrange: seed repository with an Entry built from EntryTestData::getOne().
     * - Build DeleteEntryRequest via fromArray(['id' => <uuid>]).
     * - Expect validator->validate() to be called once with the request.
     * - Execute use case; assert response echoes id; assert repository findById() returns null after deletion.
     *
     * @return void
     * @covers \Daylog\Application\UseCases\Entries\DeleteEntry\DeleteEntry::execute
     * 
     * @group UC-DeleteEntry
     */
    public function testHappyPathDeletesEntryAndReturnsResponseDto(): void
    {
        // Arrange
        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        $repo = new FakeEntryRepository();
        $repo->save($entry);

        $requestData = ['id' => $data['id']];
        /** @var DeleteEntryRequestInterface $request */
        $request = DeleteEntryRequest::fromArray($requestData);

        $validatorInterface = DeleteEntryValidatorInterface::class;
        $validator          = $this->createMock($validatorInterface);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($request);

        // Act
        $useCase  = new DeleteEntry($repo, $validator);
        $response = $useCase->execute($request);

        // Assert: response echoes the same UUID and it's a valid UUID
        $entry     = $response->getEntry();
        $entryId   = $entry->getId();
        $isValidId = UuidGenerator::isValid($entryId);
        $this->assertTrue($isValidId);
        $this->assertSame($data['id'], $entryId);

        // Assert: entity is removed from storage
        $foundAfter = $repo->findById($entryId);
        $this->assertNull($foundAfter);
    }

    /**
     * Error path: validator throws, repository is not touched (no deletion attempted).
     *
     * Scenario:
     * - Arrange: seed repository with an entry; create request; mock validator to throw DomainValidationException.
     * - Act: execute use case and expect exception.
     * - Assert: the seeded entry still exists in FakeEntryRepository after the failed call.
     *
     * @return void
     * @covers \Daylog\Application\UseCases\Entries\DeleteEntry\DeleteEntry::execute
     */
    public function testValidationErrorDoesNotTouchRepository(): void
    {
        // Arrange
        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        $repo = new FakeEntryRepository();
        $repo->save($entry);

        $requestData = ['id' => $data['id']];
        
        /** @var DeleteEntryRequestInterface $request */
        $request = DeleteEntryRequest::fromArray($requestData);

        $validatorInterface = DeleteEntryValidatorInterface::class;
        $validator          = $this->createMock($validatorInterface);

        $errorCode = 'ID_INVALID';
        $exception = new DomainValidationException($errorCode);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willThrowException($exception);

        $this->expectException(DomainValidationException::class);

        // Act
        $useCase = new DeleteEntry($repo, $validator);
        $useCase->execute($request);

        // Assert: entity still exists (no delete happened)
        $id          = $data['id'];
        $stillExists = $repo->findById($id);
        $this->assertNotNull($stillExists);
    }

   /**
     * Error path: repository returns null (entity not found), use case throws ENTRY_NOT_FOUND.
     *
     * Mechanics:
     * - Arrange: seed repository with an unrelated entry A.
     * - Build request with a different, valid UUID B (not present in repo).
     * - Expect validator->validate() to be called once and pass (no exception).
     * - Execute use case; expect DomainValidationException with ENTRY_NOT_FOUND.
     * - Assert: entry A still exists in repository after the failure.
     *
     * @return void
     * @covers \Daylog\Application\UseCases\Entries\DeleteEntry\DeleteEntry::execute
     */
    public function testNotFoundThrowsAndDoesNotDeleteOtherEntries(): void
    {
        // Arrange
        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        $repo = new FakeEntryRepository();
        $repo->save($entry);

        // Use a different, valid UUID that is not in the repo
        $notFoundId = UuidGenerator::generate();

        $requestData = ['id' => $notFoundId];
        /** @var DeleteEntryRequestInterface $request */
        $request = DeleteEntryRequest::fromArray($requestData);

        $validatorInterface = DeleteEntryValidatorInterface::class;
        $validator = $this->createMock($validatorInterface);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($request);

        $useCase = new DeleteEntry($repo, $validator);

        $this->expectException(DomainValidationException::class);
        $this->expectExceptionMessage('ENTRY_NOT_FOUND');

        // Act
        $useCase->execute($request);

        // Assert: the seeded, unrelated entry still exists
        $expectedId = $data['id'];
        $stillThere = $repo->findById($expectedId);

        $this->assertNotNull($stillThere);
    }    
}
