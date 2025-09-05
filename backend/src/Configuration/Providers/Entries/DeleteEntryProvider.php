<?php
declare(strict_types=1);

namespace Daylog\Configuration\Providers\Entries;

use Daylog\Configuration\Bootstrap\SqlFactory;

use Daylog\Application\UseCases\Entries\DeleteEntry;
use Daylog\Application\UseCases\Entries\DeleteEntryInterface;

use Daylog\Application\Validators\Entries\DeleteEntry\DeleteEntryValidator;
use Daylog\Application\Validators\Entries\DeleteEntry\DeleteEntryValidatorInterface;

use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Infrastructure\Repositories\Entries\EntryRepository;

use Daylog\Domain\Interfaces\Entries\EntryStorageInterface;
use Daylog\Infrastructure\Storage\Entries\EntryStorage;
use Daylog\Infrastructure\Storage\Entries\EntryModel;

/**
 * Provider for UC-4 DeleteEntry.
 *
 * Purpose:
 * Compose the dependency chain for deleting a diary entry by UUIDv4:
 * DB\SQL connection → Model → Storage → Repository → Validator → Use Case.
 * This wiring is centralized and framework-agnostic, suitable for integration tests and production.
 *
 * Mechanics:
 * - Builds a real DB connection via SqlFactory.
 * - Wraps the connection with EntryModel and EntryStorage abstractions.
 * - Provides a concrete EntryRepository implementation.
 * - Instantiates DeleteEntryValidator to enforce id format rules.
 * - Returns a fully wired DeleteEntry use case.
 *
 * @return DeleteEntryInterface Fully wired use case ready for execution.
 */
final class DeleteEntryProvider
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
     * Build configured UC-DeleteEntry use case.
     *
     * @return DeleteEntryInterface
     */
    public static function useCase(): DeleteEntryInterface
    {
        $repo      = self::repository();
        $validator = self::validator();

        $useCase = new DeleteEntry($repo, $validator);
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
     * @return DeleteEntryValidatorInterface
     */
    private static function validator(): DeleteEntryValidatorInterface
    {
        $validator = new DeleteEntryValidator();
        return $validator;
    }
}
