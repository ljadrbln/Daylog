<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\UpdateEntry;

use Codeception\Test\Unit;
use Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry;
use Daylog\Application\Validators\Entries\UpdateEntry\UpdateEntryValidatorInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Tests\Support\Fakes\FakeEntryRepository;

/**
 * Base wiring for UC-5 UpdateEntry unit tests.
 *
 * Purpose:
 * Provide helpers for creating a fake repository and validator mocks
 * to keep scenario tests focused and consistent.
 *
 * Mechanics:
 * - Uses FakeEntryRepository (in-memory) for persistence surface.
 * - Provides helpers to create validator mocks with expected behavior.
 */
abstract class BaseUpdateEntryUnitTest extends Unit
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
     * @return UpdateEntryValidatorInterface
     */
    protected function makeValidatorOk(): UpdateEntryValidatorInterface
    {
        $validatorClass = UpdateEntryValidatorInterface::class;
        $validator      = $this->createMock($validatorClass);

        $validator
            ->expects($this->once())
            ->method('validate');

        return $validator;
    }

    /**
     * Create a validator mock that throws DomainValidationException with a given code.
     *
     * @param string $errorCode Domain error code to throw (e.g. TITLE_REQUIRED).
     * 
     * @return UpdateEntryValidatorInterface
     */
    protected function makeValidatorThrows(string $errorCode): UpdateEntryValidatorInterface
    {
        $validatorClass = UpdateEntryValidatorInterface::class;
        $validator      = $this->createMock($validatorClass);

        $exception = new DomainValidationException($errorCode);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->willThrowException($exception);

        return $validator;
    }

    /**
     * Build the use case with given repo and validator.
     *
     * @param EntryRepositoryInterface $repo
     * @param UpdateEntryValidatorInterface $validator
     * 
     * @return UpdateEntry
     */
    protected function makeUseCase(EntryRepositoryInterface $repo, UpdateEntryValidatorInterface $validator): UpdateEntry
    {
        $useCase = new UpdateEntry($repo, $validator);

        return $useCase;
    }
}
