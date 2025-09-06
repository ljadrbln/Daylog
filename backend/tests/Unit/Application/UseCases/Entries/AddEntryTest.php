<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries;

use Codeception\Test\Unit;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;

use Daylog\Application\Validators\Entries\AddEntry\AddEntryValidatorInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\UseCases\Entries\AddEntry\AddEntry;

use Daylog\Domain\Services\UuidGenerator;
use Daylog\Domain\Services\DateService;

use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Fakes\FakeEntryRepository;

/**
 * Unit tests for UC-1 AddEntry.
 *
 * Purpose: verify that AddEntry correctly calls repository on valid data,
 * and does not touch repository when validator throws.
 *
 * Mechanics:
 * - Uses mocked repository and mocked validator.
 * - Does not test actual validation logic (covered in AddEntryValidatorTest).
 *
 * @covers \Daylog\Application\UseCases\Entries\AddEntry\AddEntry
 * @group UC-AddEntry
 */
final class AddEntryTest extends Unit
{
    /**
     * Happy path: validates request, delegates save to repository, and returns a response DTO.
     *
     * Mechanics:
     * - Data source comes from EntryTestData::getOne() (title, body, date).
     * - Validator is expected to run once on AddEntryRequestInterface.
     * - Repository returns a payload array; use case maps it to AddEntryResponseInterface.
     * - We assert that UUID is valid and all fields are propagated correctly.
     *
     * @return void
     * @covers \Daylog\Application\UseCases\Entries\AddEntry\AddEntry
     */
    public function testHappyPathSavesEntryAndReturnsResponseDto(): void
    {
        // Arrange
        $data  = EntryTestData::getOne();
        
        $request = AddEntryRequest::fromArray($data);
        $repo    = new FakeEntryRepository();

        $validatorClass = AddEntryValidatorInterface::class;
        $validator      = $this->createMock($validatorClass);
        $validator
            ->expects($this->once())
            ->method('validate');

        // Act
        $useCase  = new AddEntry($repo, $validator);
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
     * - Arrange: build AddEntryRequest; use FakeEntryRepository; mock validator to throw DomainValidationException.
     * - Act: execute use case inside try/catch.
     * - Assert: repository saveCalls() remains 0; exception class is correct.
     *
     * @covers \Daylog\Application\UseCases\Entries\AddEntry\AddEntry::execute
     */
    public function testValidationErrorDoesNotTouchRepository(): void
    {
        // Arrange
        $data = EntryTestData::getOne();

        /** @var AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        // Use fake repo (in-memory), no mocks for repo here
        $repo = new FakeEntryRepository();

        // Validator mock that throws
        $validatorInterface = AddEntryValidatorInterface::class;
        $validator          = $this->createMock($validatorInterface);

        $errorCode = 'TITLE_REQUIRED';
        $exception = new DomainValidationException($errorCode);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willThrowException($exception);

        // Assert
        $this->expectException(DomainValidationException::class); 
    
        // Act
        $useCase = new AddEntry($repo, $validator);
        $useCase->execute($request);

        // Assert that repository was never touched
        $saveCalls = $repo->getSaveCalls();
        $this->assertSame(0, $saveCalls);        
    }
}