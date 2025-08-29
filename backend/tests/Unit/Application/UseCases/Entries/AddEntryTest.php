<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries;

use Codeception\Test\Unit;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryResponseInterface;
use Daylog\Application\Normalization\Entries\AddEntryInputNormalizer;

use Daylog\Application\Validators\Entries\AddEntry\AddEntryValidatorInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\UseCases\Entries\AddEntry;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Domain\Services\UuidGenerator;

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
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
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
     * @covers \Daylog\Application\UseCases\Entries\AddEntry
     */
    public function testHappyPathSavesEntryAndReturnsResponseDto(): void
    {
        // Arrange
        $data  = EntryTestData::getOne();
        
        $normalizer = new AddEntryInputNormalizer();
        $normalized = $normalizer->normalize($data);

        $request = AddEntryRequest::fromArray($normalized);
        $repoClass = EntryRepositoryInterface::class;
        $repo      = $this->createMock($repoClass);

        $repo
            ->expects($this->once())
            ->method('save');

        $validatorClass = AddEntryValidatorInterface::class;
        $validator      = $this->createMock($validatorClass);
        $validator
            ->expects($this->once())
            ->method('validate');

        // Act
        $useCase  = new AddEntry($repo, $validator);
        $response = $useCase->execute($request);

        /** Assert **/
        $id        = $response->getId();
        $isValidId = UuidGenerator::isValid($id);
        $this->assertTrue($isValidId);

        $this->assertSame($data['title'],     $response->getTitle());
        $this->assertSame($data['body'],      $response->getBody());
        $this->assertSame($data['date'],      $response->getDate());
        $this->assertSame($data['createdAt'], $response->getCreatedAt());
        $this->assertSame($data['updatedAt'], $response->getUpdatedAt());
    }

    /**
     * Error path: validator throws, repository not called.
     */
    public function testValidationErrorDoesNotTouchRepository(): void
    {
        /** Arrange **/
        $data  = EntryTestData::getOne();

        /** @var AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        $repoClass = EntryRepositoryInterface::class;
        $repoMock  = $this->createMock($repoClass);

        $repoMock
            ->expects($this->never())
            ->method('save');

        $validatorInterface = AddEntryValidatorInterface::class;
        $validatorMock      = $this->createMock($validatorInterface);

        $exception = new DomainValidationException(['TITLE_REQUIRED']);
        $validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($request)
            ->willThrowException($exception);

        $uc = new AddEntry($repoMock, $validatorMock);

        /** Assert **/
        $this->expectException(DomainValidationException::class);

        /** Act **/
        $uc->execute($request);
    }
}