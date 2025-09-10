<?php
declare(strict_types=1);

namespace Daylog\Configuration\Providers\Entries;

use Daylog\Configuration\Bootstrap\SqlFactory;

use Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry;
use Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntryInterface;

use Daylog\Application\Validators\Entries\UpdateEntry\UpdateEntryValidator;
use Daylog\Application\Validators\Entries\UpdateEntry\UpdateEntryValidatorInterface;

use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Infrastructure\Repositories\Entries\EntryRepository;

use Daylog\Domain\Interfaces\Entries\EntryStorageInterface;
use Daylog\Infrastructure\Storage\Entries\EntryStorage;
use Daylog\Infrastructure\Storage\Entries\EntryModel;

/**
 * Provider for UC-5 UpdateEntry.
 *
 * Purpose:
 * Compose the dependency chain (DB\SQL → Model → Storage → Repository → Validator → UseCase)
 * for the UpdateEntry use case using a single source of truth for the database connection.
 *
 * Mechanics:
 * - Get a shared DB connection from SqlFactory.
 * - Build EntryModel → EntryStorage → EntryRepository.
 * - Create UpdateEntryValidator.
 * - Wire everything into UpdateEntry and return the fully-configured instance.
 *
 * @return UpdateEntryInterface Fully wired use case ready for execution.
 */
final class UpdateEntryProvider
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
     * Build configured UC-5 use case.
     *
     * @return UpdateEntryInterface
     */
    public static function useCase(): UpdateEntryInterface
    {
        $repo      = self::repository();
        $validator = self::validator();

        $useCase = new UpdateEntry($repo, $validator);
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
     * @return UpdateEntryValidatorInterface
     */
    private static function validator(): UpdateEntryValidatorInterface
    {
        $validator = new UpdateEntryValidator();
        return $validator;
    }
}
