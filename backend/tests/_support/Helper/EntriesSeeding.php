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
     * @param array<int,array{
     *     id: string,
     *     title: string,
     *     body: string,
     *     date: string,
     *     createdAt?: string|null,
     *     updatedAt?: string|null
     * }> $rows Rows of entry data (as associative arrays).
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
     * @param array<int,array{
     *     id: string,
     *     title: string,
     *     body: string,
     *     date: string,
     *     createdAt?: string|null,
     *     updatedAt?: string|null
     * }> $rows Rows of entry data (as associative arrays).
     * @return void
     */
    public static function intoDb(array $rows): void
    {
        $result = [];

        foreach ($rows as $row) {
            $id    = $row['id'];            
            $title = $row['title'];
            $body  = $row['body'];
            $date  = $row['date'];

            EntryFixture::insertOne($id, $title, $body, $date);

            $result[] = $row;
        }
    }
}
