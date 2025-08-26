<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Helper;

use Daylog\Domain\Services\UuidGenerator;
use Daylog\Infrastructure\Storage\Entries\EntryFieldMapper;

final class EntryRowHelper
{
    /**
     * Generate rows without touching DB (fixture-like behavior).
     *
     * Mechanics:
     * - Base date = today (Y-m-d).
     * - For i-th row: date = base + i*$step days.
     * - createdAt/updatedAt = "<date> 10:00:00".
     * - id = UuidGenerator::generate().
     * - Returns camelCase by default; pass $shape='snake' for DB shape.
     *
     * @param int    $count    Number of rows (>=1).
     * @param int    $step     Day step between rows (can be 0).
     * @param string $title    Default title.
     * @param string $body     Default body.
     * @param string $shape    'camel' (DTO) or 'snake' (DB).
     * @return array<int,array<string,string>>
     */
    public static function generateRows(
        int $count,
        int $step = 0,
        string $title = 'Valid title',
        string $body  = 'Valid body',
        string $shape = 'camel'
    ): array {
        if ($count < 1) {
            $message = 'count must be >= 1.';
            throw new \InvalidArgumentException($message);
        }

        $baseDate = date('Y-m-d');

        $rows = [];
        for ($i = 0; $i < $count; $i++) {
            $delta   = $i * $step;
            $tsUnix  = strtotime($baseDate . ' +' . $delta . ' day');
            $date    = date('Y-m-d', $tsUnix);
            $stamp   = $date . ' 10:00:00';

            $id      = UuidGenerator::generate();

            $camel = [
                'id'        => $id,
                'title'     => $title,
                'body'      => $body,
                'date'      => $date,
                'createdAt' => $stamp,
                'updatedAt' => $stamp,
            ];

            $row = $shape === 'snake'
                ? EntryFieldMapper::toDbRow($camel)
                : $camel;

            $rows[] = $row;
        }

        return $rows;
    }
}
