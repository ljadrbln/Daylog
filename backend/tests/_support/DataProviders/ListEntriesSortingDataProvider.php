<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\DataProviders;

/**
 * Data provider for UC-2 AC-05 sorting scenarios.
 *
 * Purpose:
 * Supply compact, human-readable cases for sorting by timestamp fields. The test
 * converts symbolic markers to runtime UUIDs after seeding.
 *
 * Mechanics:
 * - Each case returns [field, direction, expected order markers].
 *
 * @return array<string, array{0:string,1:string}> field, value
 */
trait ListEntriesSortingDataProvider
{
    /**
     * Provide timestamp sorting cases (field × direction).
     *
     * @return array<string, array{0:string,1:'ASC'|'DESC'}>
     */
    public function provideTimestampSortingCases(): array
    {
        $cases = [
            'createdAt ASC returns oldest first'           => ['createdAt', 'ASC'],
            'createdAt DESC returns newest first'          => ['createdAt', 'DESC'],
            'updatedAt ASC returns earliest updates first' => ['updatedAt', 'ASC'],
            'updatedAt DESC returns latest updates first'  => ['updatedAt', 'DESC'],
        ];

        return $cases;
    }
}