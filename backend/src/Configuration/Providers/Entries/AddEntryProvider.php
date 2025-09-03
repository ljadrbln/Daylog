<?php
declare(strict_types=1);

namespace Daylog\Configuration\Providers\Entries;

use Daylog\Configuration\Bootstrap\SqlFactory;

use Daylog\Application\UseCases\Entries\AddEntry;
use Daylog\Application\UseCases\Entries\AddEntryInterface;

use Daylog\Application\Validators\Entries\AddEntry\AddEntryValidator;
use Daylog\Application\Validators\Entries\AddEntry\AddEntryValidatorInterface;

use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Infrastructure\Repositories\Entries\EntryRepository;

use Daylog\Domain\Interfaces\Entries\EntryStorageInterface;
use Daylog\Infrastructure\Storage\Entries\EntryStorage;
use Daylog\Infrastructure\Storage\Entries\EntryModel;

/**
 * Provider for UC-1 AddEntry.
 *
 * Purpose:
 * Compose the dependency chain (DB\SQL => Model => Storage => Repository => Validator => UseCase).
 *
 * @return AddEntryInterface Fully wired use case ready for execution.
 */
final class AddEntryProvider
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
     * Build configured UC-1 use case.
     *
     * @return AddEntryInterface
     */
    public static function useCase(): AddEntryInterface
    {
        $repo      = self::repository();
        $validator = self::validator();

        $useCase = new AddEntry($repo, $validator);
        return $useCase;
    }

    /**
     * Build repository instance.
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
     * @return AddEntryValidatorInterface
     */
    private static function validator(): AddEntryValidatorInterface
    {
        $validator = new AddEntryValidator();
        return $validator;
    }
}
