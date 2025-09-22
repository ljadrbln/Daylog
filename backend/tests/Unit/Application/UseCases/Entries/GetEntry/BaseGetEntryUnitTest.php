<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\GetEntry;

use Daylog\Application\UseCases\Entries\GetEntry\GetEntry;
use Daylog\Application\Validators\Entries\GetEntry\GetEntryValidatorInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Tests\Support\Fakes\FakeEntryRepository;
use Daylog\Tests\Unit\Application\UseCases\Entries\BaseEntryUseCaseUnitTest;

/**
 * Base wiring for UC-3 GetEntry unit tests.
 *
 * Purpose:
 * Provide helpers for creating a fake repository, validator mocks,
 * and wiring the use case, so scenario tests stay focused.
 *
 * Mechanics:
 * - Uses FakeEntryRepository (in-memory) as persistence surface.
 * - Provides helpers for validator mocks: success or throwing with given error code.
 */
abstract class BaseGetEntryUnitTest extends BaseEntryUseCaseUnitTest
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
     * @return GetEntryValidatorInterface
     */
    protected function makeValidatorOk(): GetEntryValidatorInterface
    {
        $validatorClass = GetEntryValidatorInterface::class;
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
     * @return GetEntryValidatorInterface
     */
    protected function makeValidatorThrows(string $errorCode): GetEntryValidatorInterface
    {
        $validatorClass = GetEntryValidatorInterface::class;
        $validator      = $this->createMock($validatorClass);

        $exception = new DomainValidationException($errorCode);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->willThrowException($exception);

        return $validator;
    }

    /**
     * Build the GetEntry use case with given repo and validator.
     *
     * @param EntryRepositoryInterface   $repo
     * @param GetEntryValidatorInterface $validator
     * @return GetEntry
     */
    protected function makeUseCase(EntryRepositoryInterface $repo, GetEntryValidatorInterface $validator): GetEntry
    {
        $useCase = new GetEntry($repo, $validator);
        return $useCase;
    }
}
