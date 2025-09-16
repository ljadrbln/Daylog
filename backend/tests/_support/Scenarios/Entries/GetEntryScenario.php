<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Scenarios\Entries;

use Daylog\Domain\Models\Entries\Entry;
use Daylog\Tests\Support\Helper\EntryTestData;

/**
 * Scenario data for GetEntry use case.
 *
 * Purpose:
 *   Provide a single source of truth for UC-3 / AC-01 (happy path) across Unit, Integration, and Functional tests.
 *   Ensures deterministic payload and a ready-made expected domain Entry for equality assertions.
 *
 * Mechanics:
 *   - Build a single valid row via EntryTestData::getOne();
 *   - Use the same id to construct the request in tests;
 *   - Prepare an expected Entry instance with Entry::fromArray() for strict equality checks.
 */
final class GetEntryScenario
{
    /**
     * AC-01: Happy path — given an existing id, the system returns the Entry.
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
     *   targetId: string,
     *   expected: Entry
     * }
     */
    public static function ac01HappyPath(): array
    {
        $row  = EntryTestData::getOne();
        $rows = [$row];

        $targetId = $row['id'];
        $expected = Entry::fromArray($row);
        
        $dataset = [
            'rows'     => $rows,
            'targetId' => $targetId,
            'expected' => $expected,
        ];

        return $dataset;
    }
}
