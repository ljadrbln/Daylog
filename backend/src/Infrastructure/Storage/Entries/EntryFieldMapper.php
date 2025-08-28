<?php
declare(strict_types=1);

namespace Daylog\Infrastructure\Storage\Entries;

use Daylog\Domain\Models\Entries\Entry;

/**
 * Field mapper between domain/application camelCase and DB snake_case.
 */
final class EntryFieldMapper
{
    /** @var array<string,string> */
    private const TO_DB = [
        'id'        => 'id',
        'date'      => 'date',
        'title'     => 'title',
        'body'      => 'body',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
    ];

    /** @var array<int,string> */
    private const ALLOWED_ORDER_DIRS = ['ASC', 'DESC'];

    /**
     * Get flipped map (DB -> domain) with static caching.
     *
     * @return array<string,string>
     */
    private static function flipped(): array
    {
        static $flipped = null;

        if ($flipped === null) {
            $flipped = array_flip(self::TO_DB);
        }

        $map = $flipped;
        return $map;
    }

    /**
     * Map domain/application field to DB field.
     *
     * @param string $field CamelCase name
     * @return string Snake_case name
     */
    public static function toDbField(string $field): string
    {
        $mapped = self::TO_DB[$field] ?? $field;
        return $mapped;
    }

    /**
     * Map DB field to domain/application field.
     *
     * @param string $field Snake_case name
     * @return string CamelCase name
     */
    public static function fromDbField(string $field): string
    {
        $flipped = self::flipped();
        $mapped  = $flipped[$field] ?? $field;
        return $mapped;
    }

    /**
     * Map DB row (snake_case) to DTO shape (camelCase).
     *
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    public static function fromDbRow(array $row): array
    {
        $mapped = [];

        foreach ($row as $key => $value) {
            $camel = self::fromDbField($key);
            $mapped[$camel] = $value;
        }

        return $mapped;
    }

    /**
     * Map DTO row (camelCase) to DB shape (snake_case).
     *
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    public static function toDbRow(array $row): array
    {
        $mapped = [];

        foreach ($row as $key => $value) {
            $snake = self::toDbField($key);
            $mapped[$snake] = $value;
        }

        return $mapped;
    }

    /**
     * Map a Domain Entry into a DB row (snake_case).
     *
     * Purpose:
     * Keep Domain free of array serialization; build storage row here.
     *
     * @param Entry $entry Domain entity with id/timestamps already set.
     * @return array{
     *     id:string,
     *     title:string,
     *     body:string,
     *     date:string,
     *     created_at:string,
     *     updated_at:string
     * }
     */
    public static function toDbRowFromEntry(Entry $entry): array
    {
        $row = [
            'id'         => $entry->getId(),
            'title'      => $entry->getTitle(),
            'body'       => $entry->getBody(),
            'date'       => $entry->getDate(),
            'created_at' => $entry->getCreatedAt(),
            'updated_at' => $entry->getUpdatedAt(),
        ];

        return $row;
    }

    /**
     * Build F3 'order' string from domain descriptors (safe allow-list).
     *
     * Purpose:
     * Convert domain sort descriptors to a DB order clause while enforcing
     * field allow-list and direction allow-list to avoid invalid SQL tokens.
     *
     * @param array<int,array{field:string,direction:string}> $descriptors
     * @return string Comma-separated order clause with DB fields ('' when none valid).
     */
    public static function buildOrderSafe(array $descriptors): string
    {
        $parts = [];

        foreach ($descriptors as $desc) {
            $fieldDomain = $desc['field'];
            $dirRaw      = $desc['direction'];

            // Only allow fields explicitly mapped in TO_DB
            $isKnownField = array_key_exists($fieldDomain, self::TO_DB);

            // Normalize direction to uppercase and clamp to allow-list
            $dirUpper  = strtoupper($dirRaw);
            $isAllowed = in_array($dirUpper, self::ALLOWED_ORDER_DIRS, true);

            if (!$isKnownField || !$isAllowed) {
                continue;
            }

            $fieldDb = self::toDbField($fieldDomain);

            $part = sprintf('%s %s', $fieldDb, $dirUpper);
            $parts[] = $part;
        }

        $order = implode(', ', $parts);
        return $order;
    }
}
