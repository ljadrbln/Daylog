<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Helper;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Tests\Support\Fixture\EntryFixture;

/**
 * Seeding helpers for different layers (fake repo vs real DB).
 *
 * Purpose:
 *   Persist the same dataset either to a fake repository (Unit)
 *   or into the real database via fixtures (Integration).
 */
final class EntriesSeeding
{
    /**
     * Put scenario rows into a fake repository (Unit tests).
     *
     * @param EntryRepositoryInterface $repo
     * @param array<int,array>         $rows
     * @return void
     */
    public static function intoFakeRepo(EntryRepositoryInterface $repo, array $rows): void
    {
        foreach ($rows as $row) {
            $entry = Entry::fromArray($row);
            $repo->save($entry);
        }
    }

    /**
     * Insert scenario rows into the real DB (Integration tests) using fixture.
     *
     * @param array<int,array> $rows
     * @return array<int,array> Rows with DB-assigned values if fixture mutates anything
     */
    public static function intoDb(array $rows): array
    {
        $result = [];

        foreach ($rows as $row) {
            /** @var string */
            $title = $row['title'];

            /** @var string */
            $body  = $row['body'];

            /** @var string */
            $date  = $row['date'];

            $id = EntryFixture::insertOne($title, $body, $date);
            $row['id'] = $id;

            $result[] = $row;
        }

        return $result;
    }
}
