<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries;

/**
 * Sanitizer for UC-1 Add Entry transport input.
 *
 * Purpose:
 * - Apply BR-1 (Trimming) consistently to all string fields.
 * - Keep AddEntryRequestFactory focused on type checks and DTO creation.
 *
 * Mechanics:
 * - Called inside AddEntryRequestFactory::fromArray().
 * - Does not perform any business validation.
 */
final class AddEntrySanitizer
{
    /**
     * Apply BR-1 trimming to raw params.
     *
     * @param array{
     *     title:string,
     *     body:string,
     *     date:string
     * } $params
     *
     * @return array{
     *     title:string,
     *     body:string,
     *     date:string
     * }
     */
    public static function sanitize(array $params): array
    {
        $title = trim($params['title']);
        $body  = trim($params['body']);
        $date  = trim($params['date']);

        return [
            'title' => $title,
            'body'  => $body,
            'date'  => $date,
        ];
    }
}
