<?php

declare(strict_types=1);

namespace Daylog\Configuration\Providers\Entries;

use Daylog\Configuration\Bootstrap\SqlFactory;

use Daylog\Application\UseCases\Entries\ListEntries\ListEntries;
use Daylog\Application\UseCases\Entries\ListEntries\ListEntriesInterface;

use Daylog\Application\Validators\Entries\ListEntries\ListEntriesValidator;
use Daylog\Application\Validators\Entries\ListEntries\ListEntriesValidatorInterface;

use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Infrastructure\Repositories\Entries\EntryRepository;

use Daylog\Domain\Interfaces\Entries\EntryStorageInterface;
use Daylog\Infrastructure\Storage\Entries\EntryStorage;
use Daylog\Infrastructure\Storage\Entries\EntryModel;

/**
 * Provider for UC-1 ListEntries.
 *
 * Purpose:
 * Compose the dependency chain (DB\SQL => Model => Storage => Repository => Validator => UseCase).
 *
 * @return ListEntriesInterface Fully wired use case ready for execution.
 */
final class ListEntriesProvider
{
    /**
     * Disallow instantiation (static provider).
     *
     * @return void
     */
    private function __construct()
    {
        // Intentionally empty.
    }

    /**
     * Build configured UC-2 use case.
     *
     * @return ListEntriesInterface
     */
    public static function useCase(): ListEntriesInterface
    {
        $repo      = self::repository();
        $validator = self::validator();

        $useCase = new ListEntries($repo, $validator);
        return $useCase;
    }

    /**
     * Build repository instance.
     *
     * Mechanics:
     *  - Depends on EntryStorageInterface produced by storage().
     *
     * @return EntryRepositoryInterface
     */
    private static function repository(): EntryRepositoryInterface
    {
        $storage = self::storage();
        $repo    = new EntryRepository($storage);

        return $repo;
    }

    /**
     * Build storage instance.
     *
     * Mechanics:
     *  - Uses SqlFactory::get() to obtain shared DB\SQL.
     *  - Wraps it into EntryModel and then EntryStorage.
     *
     * @return EntryStorageInterface
     */
    private static function storage(): EntryStorageInterface
    {
        $sql   = SqlFactory::get();
        $model = new EntryModel($sql);

        $storage = new EntryStorage($model);
        return $storage;
    }

    /**
     * Build validator instance.
     *
     * Purpose:
     *  Provide the validator specific to UC-2 ListEntries.
     *
     * @return ListEntriesValidatorInterface
     */
    private static function validator(): ListEntriesValidatorInterface
    {
        $validator = new ListEntriesValidator();
        return $validator;
    }
}
