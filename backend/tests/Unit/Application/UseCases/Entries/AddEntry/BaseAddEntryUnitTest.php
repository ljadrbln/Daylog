<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\AddEntry;

use Codeception\Test\Unit;
use Daylog\Application\UseCases\Entries\AddEntry\AddEntry;
use Daylog\Application\Validators\Entries\AddEntry\AddEntryValidatorInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Tests\Support\Fakes\FakeEntryRepository;

/**
 * Base wiring for UC-1 AddEntry unit tests.
 *
 * Purpose:
 * Provide helpers to assemble the use case with a fake repository
 * and validator mocks to keep scenario tests focused and consistent.
 *
 * Mechanics:
 * - Uses FakeEntryRepository (in-memory) for persistence surface.
 * - Exposes helpers for a "success" validator and a "throws" validator.
 */
abstract class BaseAddEntryUnitTest extends Unit
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
     * @return AddEntryValidatorInterface
     */
    protected function makeValidatorOk(): AddEntryValidatorInterface
    {
        $validatorClass = AddEntryValidatorInterface::class;
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
     * @return AddEntryValidatorInterface
     */
    protected function makeValidatorThrows(string $errorCode): AddEntryValidatorInterface
    {
        $validatorClass = AddEntryValidatorInterface::class;
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
     * @param EntryRepositoryInterface  $repo
     * @param AddEntryValidatorInterface $validator
     * @return AddEntry
     */
    protected function makeUseCase(EntryRepositoryInterface $repo, AddEntryValidatorInterface $validator): AddEntry
    {
        $useCase = new AddEntry($repo, $validator);
        return $useCase;
    }
}
