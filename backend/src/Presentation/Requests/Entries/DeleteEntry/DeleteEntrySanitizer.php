<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries\DeleteEntry;

/**
 * Sanitizer for UC-3 Delete Entry transport input.
 *
 * Purpose:
 * - Apply BR-1 (Trimming) consistently to id field.
 * - Keep DeleteEntryRequestFactory focused on type checks and DTO creation.
 *
 * Mechanics:
 * - Called inside DeleteEntryRequestFactory::fromArray().
 * - Does not perform any business validation.
 */
final class DeleteEntrySanitizer
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
