<?php
declare(strict_types=1);

namespace Daylog\Tests\Unit\Application\UseCases\Entries\ListEntries;

use Codeception\Test\Unit;
use Daylog\Application\UseCases\Entries\ListEntries\ListEntries;
use Daylog\Application\Validators\Entries\ListEntries\ListEntriesValidatorInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Tests\Support\Fakes\FakeEntryRepository;

/**
 * Base wiring for UC-2 ListEntries unit tests.
 *
 * Purpose:
 * Provide helpers for creating a fake repository, validator mocks,
 * and wiring the use case, so scenario tests stay focused.
 *
 * Mechanics:
 * - Uses FakeEntryRepository (in-memory) as the persistence surface.
 * - Provides helpers for validator mocks: success or throwing with given error code.
 */
abstract class BaseListEntriesUnitTest extends Unit
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
     * @return ListEntriesValidatorInterface
     */
    protected function makeValidatorOk(): ListEntriesValidatorInterface
    {
        $validatorClass = ListEntriesValidatorInterface::class;
        $validator      = $this->createMock($validatorClass);

        $validator
            ->expects($this->once())
            ->method('validate');

        return $validator;
    }

    /**
     * Create a validator mock that throws DomainValidationException with a given code.
     *
     * @param string $errorCode Domain error code to throw (e.g. DATE_INVALID).
     * @return ListEntriesValidatorInterface
     */
    protected function makeValidatorThrows(string $errorCode): ListEntriesValidatorInterface
    {
        $validatorClass = ListEntriesValidatorInterface::class;
        $validator      = $this->createMock($validatorClass);

        $exception = new DomainValidationException($errorCode);

        $validator
            ->expects($this->once())
            ->method('validate')
            ->willThrowException($exception);

        return $validator;
    }

    /**
     * Build the ListEntries use case with given repo and validator.
     *
     * @param EntryRepositoryInterface       $repo
     * @param ListEntriesValidatorInterface  $validator
     * @return ListEntries
     */
    protected function makeUseCase(EntryRepositoryInterface $repo, ListEntriesValidatorInterface $validator): ListEntries
    {
        $useCase = new ListEntries($repo, $validator);

        return $useCase;
    }
}
