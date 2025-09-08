<?php

declare(strict_types=1);

namespace Daylog\Application\Transformers\Entries;

use Daylog\Domain\Models\Entries\Entry;
/**
 * Class EntryTransformer
 *
 * Immutable DTO representing a single Entry response.
 * This is a read-model object for presentation; it is not a domain entity.
 *
 * Usage:
 * - Construct via factory {@see EntryTransformer::fromArray()} using a storage/repository row.
 * - The row is assumed to be already normalized by the Application layer.
 *
 * Expected input format (row):
 * - id:        string UUID v4
 * - date:      string YYYY-MM-DD (logical entry date)
 * - title:     string
 * - body:      string
 * - createdAt: string "YYYY-MM-DD HH:MM:SS" (UTC)
 * - updatedAt: string "YYYY-MM-DD HH:MM:SS" (UTC)
 */
final class EntryTransformer
{
    /**
     * Transform a Domain Entry into an associative array.
     *
     * @param Entry $entry Domain model carrying validated fields.
     * @return array{id:string,date:string,title:string,body:string,createdAt:string,updatedAt:string}
     */
    public static function fromEntry(Entry $entry): array
    {
        $id        = $entry->getId();
        $date      = $entry->getDate();
        $title     = $entry->getTitle();
        $body      = $entry->getBody();
        $createdAt = $entry->getCreatedAt();
        $updatedAt = $entry->getUpdatedAt();

        $result = [
            'id'        => $id,
            'title'     => $title,
            'body'      => $body,
            'date'      => $date,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
        ];

        return $result;
    }
}