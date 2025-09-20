<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\DeleteEntry;

use Daylog\Tests\Unit\Application\UseCases\Entries\BaseEntryUseCaseUnitTest;

use Daylog\Application\UseCases\Entries\DeleteEntry\DeleteEntry;
use Daylog\Application\Validators\Entries\DeleteEntry\DeleteEntryValidatorInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Tests\Support\Fakes\FakeEntryRepository;

/**
 * Base wiring for UC-4 DeleteEntry unit tests.
 *
 * Purpose:
 * Provide helpers for creating a fake repository, validator mocks,
 * and wiring the use case, so scenario tests stay focused.
 *
 * Mechanics:
 * - Uses FakeEntryRepository (in-memory) as persistence surface.
 * - Provides helpers for validator mocks: success or throwing with given error code.
 */
abstract class BaseDeleteEntryUnitTest extends BaseEntryUseCaseUnitTest
{
    /**
     * Create a fresh fake repository instance.
     *
     * @return FakeEntryRepository
     */
    protected function makeRepo(): FakeEntryRepository
    {
        $repo = new FakeEntryRepository();
        return $repo;
    }

    /**
     * Create a validator mock expected to run exactly once and succeed.
     *
     * @return DeleteEntryValidatorInterface
     */
    protected function makeValidatorOk(): DeleteEntryValidatorInterface
    {
        $validatorClass = DeleteEntryValidatorInterface::class;
        $validator      = $this->createMock($validatorClass);

        $validator
            ->expects($this->once())
            ->method('validate');

        return $validator;
    }

    /**
     * Create a validator mock that throws DomainValidationException with a given code.
     *
     * @param string $errorCode Domain error code to throw (e.g. ID_INVALID, ENTRY_NOT_FOUND).
     * @return DeleteEntryValidatorInterface
     */
    protected function makeValidatorThrows(string $errorCode): DeleteEntryValidatorInterface
    {
        $validatorClass = DeleteEntryValidatorInterface::class;
        $validator      = $this->createMock($validatorClass);

        $exception = new DomainValidationException($errorCode);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->willThrowException($exception);

        return $validator;
    }

    /**
     * Build the DeleteEntry use case with given repo and validator.
     *
     * @param EntryRepositoryInterface     $repo
     * @param DeleteEntryValidatorInterface $validator
     * @return DeleteEntry
     */
    protected function makeUseCase(EntryRepositoryInterface $repo, DeleteEntryValidatorInterface $validator): DeleteEntry
    {
        $useCase = new DeleteEntry($repo, $validator);
        
        return $useCase;
    }
}
