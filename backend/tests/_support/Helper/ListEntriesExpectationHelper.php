<?php
declare(strict_types=1);

namespace Daylog\Tests\Support\Helper;

/**
 * Helpers for building expected id order in UC-2 sorting tests.
 *
 * Purpose:
 * Reuse a single implementation of expected-ids builder across Unit/Integration.
 *
 * @psalm-type Row=array{id:string,createdAt:string,updatedAt:string}
 */
trait ListEntriesExpectationHelper
{
    /**
     * Build expected id order from seeded rows using the requested sort.
     *
     * Primary: 'createdAt'|'updatedAt' with ASC|DESC.
     * Tie-breaker: createdAt DESC (stable).
     *
     * @param array<int,array<string,mixed>> $rows
     * @param string                         $sortField 'createdAt'|'updatedAt'
     * @param 'ASC'|'DESC'                   $sortDir
     * @return array<int,string>
     */
    private function buildExpectedIds(array $rows, string $sortField, string $sortDir): array
    {
        $pairs = [];

        foreach ($rows as $row) {
            $id        = $row['id'];
            $primary   = $row[$sortField];
            $createdAt = $row['createdAt'];

            $pairs[] = [
                'id'        => $id,
                'primary'   => $primary,
                'createdAt' => $createdAt,
            ];
        }

        usort(
            $pairs,
            static function (array $a, array $b) use ($sortDir): int {
                /** @var string */
                $leftPrimary = $a['primary'];
                $leftPrimary = strtotime($leftPrimary);

                /** @var string */
                $rightPrimary = $b['primary'];
                $rightPrimary = strtotime($rightPrimary);

                $cmp = $leftPrimary <=> $rightPrimary;

                if ($cmp === 0) {
                    /** @var string */
                    $leftCreated = $a['createdAt'];
                    $leftCreated = strtotime($leftCreated);

                    /** @var string */
                    $rightCreated = $b['createdAt'];
                    $rightCreated = strtotime($rightCreated);

                    $cmp = $rightCreated <=> $leftCreated; // createdAt DESC
                } elseif ($sortDir === 'DESC') {
                    $cmp = -$cmp; // invert only primary
                }

                $result = $cmp;
                return $result;
            }
        );

        $expectedIds = array_column($pairs, 'id');
        return $expectedIds;
    }
}
