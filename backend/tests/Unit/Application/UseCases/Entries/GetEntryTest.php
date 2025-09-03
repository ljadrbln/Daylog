<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries;

use Codeception\Test\Unit;

use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequest;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;

use Daylog\Application\Validators\Entries\GetEntry\GetEntryValidatorInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\UseCases\Entries\GetEntry;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Domain\Services\DateService;
use Daylog\Domain\Models\Entries\Entry;

use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Fakes\FakeEntryRepository;

/**
 * Unit tests for UC-3 GetEntry.
 *
 * Purpose: verify that GetEntry correctly calls repository on valid data,
 * and does not touch repository when validator throws.
 *
 * Mechanics:
 * - Uses mocked repository and mocked validator.
 * - Does not test actual validation logic (covered in GetEntryValidatorTest).
 *
 * @covers \Daylog\Application\UseCases\Entries\GetEntry
 */
final class GetEntryTest extends Unit
{
    /**
     * Happy path: validates request, delegates save to repository, and returns a response DTO.
     *
     * Mechanics:
     * - Data source comes from EntryTestData::getOne() (title, body, date).
     * - Validator is expected to run once on GetEntryRequestInterface.
     * - Repository returns a payload array; use case maps it to GetEntryResponseInterface.
     * - We assert that UUID is valid and all fields are propagated correctly.
     *
     * @return void
     * @covers \Daylog\Application\UseCases\Entries\GetEntry
     */
    public function testHappyPathSavesEntryAndReturnsResponseDto(): void
    {
        // Arrange
        $data  = EntryTestData::getOne();
        $entry = Entry::fromArray($data);

        $repo = new FakeEntryRepository();
        $repo->save($entry);

        $request = GetEntryRequest::fromArray($data);

        $validatorClass = GetEntryValidatorInterface::class;
        $validator      = $this->createMock($validatorClass);
        $validator
            ->expects($this->once())
            ->method('validate');

        // Act
        $useCase  = new GetEntry($repo, $validator);
        $response = $useCase->execute($request);

        // Assert
        $id        = $response->getId();
        $isValidId = UuidGenerator::isValid($id);
        $this->assertTrue($isValidId);

        $this->assertSame($data['title'], $response->getTitle());
        $this->assertSame($data['body'],  $response->getBody());
        $this->assertSame($data['date'],  $response->getDate());

        $actualCreatedAt = $response->getCreatedAt();
        $actualUpdatedAt = $response->getUpdatedAt();

        $isCreatedAtDateValid = DateService::isValidIsoUtcDateTime($actualCreatedAt);
        $this->assertTrue($isCreatedAtDateValid);
        $this->assertSame($actualCreatedAt, $actualUpdatedAt);
    }

    /**
     * Error path: validator throws, repository is not touched.
     *
     * Scenario:
     * - Arrange: build GetEntryRequest; use FakeEntryRepository; mock validator to throw DomainValidationException.
     * - Act: execute use case inside try/catch.
     * - Assert: repository saveCalls() remains 0; exception class is correct.
     *
     * @covers \Daylog\Application\UseCases\Entries\GetEntry::execute
     */
    public function testValidationErrorDoesNotTouchRepository(): void
    {
        // Arrange
        $data = EntryTestData::getOne();

        /** @var GetEntryRequestInterface $request */
        $request = GetEntryRequest::fromArray($data);

        // Use fake repo (in-memory), no mocks for repo here
        $repo = new FakeEntryRepository();

        // Validator mock that throws
        $validatorInterface = GetEntryValidatorInterface::class;
        $validator          = $this->createMock($validatorInterface);

        $errors    = ['TITLE_REQUIRED'];
        $exception = new DomainValidationException($errors);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willThrowException($exception);

        // Assert
        $this->expectException(DomainValidationException::class); 
    
        // Act
        $useCase = new GetEntry($repo, $validator);
        $useCase->execute($request);

        // Assert that repository was never touched
        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);        
    }
}