<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntryRequestInterface;
use Daylog\Application\Validators\Entries\AddEntryValidatorInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\UseCases\Entries\AddEntry;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntryHelper;

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
     * Happy path: validator passes, repository called, UUID returned.
     */
    public function testHappyPathSavesEntryAndReturnsUuid(): void
    {
        /** Arrange **/
        $data  = EntryHelper::getData();

        /** @var AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        $repoClass = EntryRepositoryInterface::class;
        $repoMock  = $this->createMock($repoClass);

        $expectedUuid = 'mocked-uuid';
        $repoMock
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Entry::class))
            ->willReturn($expectedUuid);

        $validatorInterface = AddEntryValidatorInterface::class;
        $validatorMock      = $this->createMock($validatorInterface);

        $validatorMock
            ->expects($this->once())
            ->method('validate')
            ->with($request);

        $uc = new AddEntry($repoMock, $validatorMock);

        /** Act **/
        $uuid = $uc->execute($request);

        /** Assert **/
        $this->assertSame($expectedUuid, $uuid);
    }

    /**
     * Error path: validator throws, repository not called.
     */
    public function testValidationErrorDoesNotTouchRepository(): void
    {
        /** Arrange **/
        $data  = EntryHelper::getData();

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