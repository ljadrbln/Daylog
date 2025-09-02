<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries;

/**
 * Sanitizer for UC-3 Get Entry transport input.
 *
 * Purpose:
 * - Apply BR-1 (Trimming) consistently to all string fields.
 * - Keep GetEntryRequestFactory focused on type checks and DTO creation.
 *
 * Mechanics:
 * - Called inside GetEntryRequestFactory::fromArray().
 * - Does not perform any business validation.
 */
final class GetEntrySanitizer
{
    /**
     * Apply BR-1 trimming to raw params.
     *
     * @param array{
     *     id:string
     * } $params
     *
     * @return array{
     *     id:string
     * }
     */
    public static function sanitize(array $params): array
    {
        $id = trim($params['id']);

        return [
            'id' => $id
        ];
    }
}
