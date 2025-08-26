<?php
declare(strict_types=1);

namespace Daylog\Infrastructure\Storage\Entries;

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

    /**
     * Map domain/application field to DB field.
     *
     * @param string $field CamelCase name
     * @return string Snake_case name
     */
    public static function toDbField(string $field): string
    {
        return self::TO_DB[$field] ?? $field;
    }

    /**
     * Map DB field to domain/application field.
     *
     * @param string $field Snake_case name
     * @return string CamelCase name
     */
    public static function fromDbField(string $field): string
    {
        $flipped = array_flip(self::TO_DB);
        return $flipped[$field] ?? $field;
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
     * Build F3 'order' string from domain descriptors.
     *
     * @param array<int,array{field:string,direction:string}> $descriptors
     * @return string Comma-separated order clause with DB fields.
     */
    public static function buildOrder(array $descriptors): string
    {
        $parts = [];

        foreach ($descriptors as $desc) {
            $fieldDomain = $desc['field'];
            $dirVar      = $desc['direction'];

            $fieldDb = self::toDbField($fieldDomain);

            $part = sprintf('%s %s', $fieldDb, $dirVar);
            $parts[] = $part;
        }

        $order = implode(', ', $parts);
        return $order;
    }    
}