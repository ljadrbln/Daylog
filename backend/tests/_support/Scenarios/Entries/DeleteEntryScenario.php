<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Scenarios\Entries;

use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Scenario data for DeleteEntry use case.
 *
 * Purpose:
 *   Provide a single source of truth for AC-07 (single-date exact match) across Unit, Integration, and Functional tests.
 *
 * Mechanics:
 *   - Build N rows with a fixed day step so dates differ predictably.
 *   - Choose the target date from the first row.
 *   - Compute expected IDs that MUST be returned by the UC.
 */
final class DeleteEntryScenario
{
    /**
     * AC-01: happy path returns list of items
     *
     * @return array{
     *   rows: array<int, array{
     *     id: string,
     *     title: string,
     *     body: string,
     *     date: string,
     *     createdAt?: string|null,
     *     updatedAt?: string|null
     *   }>,
     *   targetId: string
     * }
     */
    public static function ac01HappyPath(): array
    {
        $rows = EntryTestData::getMany(1, 0);
        $targetId = $rows[0]['id'];

        $dataset = [
            'rows'     => $rows,
            'targetId' => $targetId
        ];

        return $dataset;
    }
}