<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries;

use Codeception\Test\Unit;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;

use Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry;
use Daylog\Application\Validators\Entries\UpdateEntry\UpdateEntryValidatorInterface;
use Daylog\Application\Exceptions\DomainValidationException;

use Daylog\Tests\Support\Fakes\FakeEntryRepository;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Domain\Models\Entries\Entry;

use Daylog\Domain\Services\UuidGenerator;
use Daylog\Domain\Services\DateService;

/**
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class UpdateEntryTest extends Unit
{
    /**
     * Happy path: validates request, updates entry in repository, and returns a response DTO.
     *
     * Mechanics:
     * - Seed repository with an existing entry (EntryTestData::getOne()) and a generated UUID.
     * - Validator is expected to run once on UpdateEntryRequestInterface.
     * - Use case updates only provided fields; unchanged fields remain intact.
     * - We assert that timestamps respect BR-2 (updatedAt ≥ createdAt) and that title is updated.
     *
     * @return void
     * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
     */
    public function testHappyPathUpdatesEntryAndReturnsResponseDto(): void
    {
        // Arrange
        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        $request = UpdateEntryRequest::fromArray($data);
        $repo    = new FakeEntryRepository();
        $repo->save($entry);

        $validatorClass = UpdateEntryValidatorInterface::class;
        $validator      = $this->createMock($validatorClass);
        $validator
            ->expects($this->once())
            ->method('validate');

        // Act
        $useCase  = new UpdateEntry($repo, $validator);
        $response = $useCase->execute($request);

        // Assert
        $entry = $response->getEntry();

        $actualId   = $entry->getId();
        $isValidId  = UuidGenerator::isValid($actualId);
        $this->assertTrue($isValidId);
        $this->assertSame($data['id'], $actualId);

        $this->assertSame($data['title'], $entry->getTitle());
        $this->assertSame($data['body'],  $entry->getBody());
        $this->assertSame($data['date'],  $entry->getDate());

        $createdAt = $entry->getCreatedAt();
        $updatedAt = $entry->getUpdatedAt();

        $isCreatedAtValid = DateService::isValidIsoUtcDateTime($createdAt);
        $isUpdatedAtValid = DateService::isValidIsoUtcDateTime($updatedAt);

        $this->assertTrue($isCreatedAtValid);
        $this->assertTrue($isUpdatedAtValid);
        $this->assertGreaterThanOrEqual(strtotime($createdAt), strtotime($updatedAt));
    }

    /**
     * Error path: validator throws (e.g., TITLE_REQUIRED); repository must not be touched.
     *
     * @return void
     * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
     */
    public function testValidationErrorDoesNotTouchRepository(): void
    {
        $entryId = UuidGenerator::generate();

        $payload = [
            'id'    => $entryId,
            'title' => '', // invalid
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        $repo = new FakeEntryRepository();

        $validatorInterface = UpdateEntryValidatorInterface::class;
        $validator          = $this->createMock($validatorInterface);

        $errorCode = 'TITLE_REQUIRED';
        $exception = new DomainValidationException($errorCode);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willThrowException($exception);

        $this->expectException(DomainValidationException::class);

        $useCase = new UpdateEntry($repo, $validator);
        $useCase->execute($request);

        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);
    }

    /**
     * Error path: only id provided → NO_FIELDS_TO_UPDATE; repository must not be touched.
     *
     * @return void
     * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry::execute
     */
    public function testNoFieldsToUpdateTriggersDomainError(): void
    {
        $entryId = UuidGenerator::generate();

        $payload = [
            'id' => $entryId,
        ];

        /** @var UpdateEntryRequestInterface $request */
        $request = UpdateEntryRequest::fromArray($payload);

        $repo = new FakeEntryRepository();

        $validatorInterface = UpdateEntryValidatorInterface::class;
        $validator          = $this->createMock($validatorInterface);

        $errorCode = 'NO_FIELDS_TO_UPDATE';
        $exception = new DomainValidationException($errorCode);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willThrowException($exception);

        $this->expectException(DomainValidationException::class);

        $useCase = new UpdateEntry($repo, $validator);
        $useCase->execute($request);

        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);
    }
}
