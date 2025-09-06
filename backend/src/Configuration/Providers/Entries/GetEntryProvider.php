<?php
declare(strict_types=1);

namespace Daylog\Configuration\Providers\Entries;

use Daylog\Configuration\Bootstrap\SqlFactory;

use Daylog\Application\UseCases\Entries\GetEntry\GetEntry;
use Daylog\Application\UseCases\Entries\GetEntry\GetEntryInterface;

use Daylog\Application\Validators\Entries\GetEntry\GetEntryValidator;
use Daylog\Application\Validators\Entries\GetEntry\GetEntryValidatorInterface;

use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Infrastructure\Repositories\Entries\EntryRepository;

use Daylog\Domain\Interfaces\Entries\EntryStorageInterface;
use Daylog\Infrastructure\Storage\Entries\EntryStorage;
use Daylog\Infrastructure\Storage\Entries\EntryModel;

/**
 * Provider for UC-3 GetEntry.
 *
 * Purpose:
 * Compose the dependency chain (DB\SQL → Model → Storage → Repository → Validator → UseCase)
 * to retrieve a single diary entry by UUIDv4. Keeps wiring centralized and framework-agnostic.
 *
 * Mechanics:
 * - Builds a real DB connection via SqlFactory (single source of truth).
 * - Instantiates the EntryModel (thin DB mapper), then wraps it with EntryStorage.
 * - Composes EntryRepository on top of storage.
 * - Provides a validator for request id checks.
 * - Returns a fully-wired GetEntry use case.
 *
 * @return GetEntryInterface Fully wired use case ready for execution.
 */
final class GetEntryProvider
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
     * Build configured UC-3 use case.
     *
     * @return GetEntryInterface
     */
    public static function useCase(): GetEntryInterface
    {
        $repo      = self::repository();
        $validator = self::validator();

        $useCase = new GetEntry($repo, $validator);
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
     * @return GetEntryValidatorInterface
     */
    private static function validator(): GetEntryValidatorInterface
    {
        $validator = new GetEntryValidator();
        return $validator;
    }
}
