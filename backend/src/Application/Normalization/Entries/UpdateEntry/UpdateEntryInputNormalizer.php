<?php
declare(strict_types=1);

namespace Daylog\Application\Normalization\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\Clock;
use Daylog\Domain\Services\DateService;
/**
 * Normalize merged state for UC-5 UpdateEntry.
 *
 * Purpose:
 * Build a flat associative payload for Entry::fromArray() by merging the current
 * Entry snapshot with optional fields from the UpdateEntry request, preserving
 * invariants for immutable and timestamp fields.
 *
 * Mechanics:
 * - Assumes transport sanitization (BR-1 trimming) is already applied in Presentation.
 * - Preserve id and createdAt from the current Entry.
 * - For each optional field (title, body, date), take the request value if provided;
 *   otherwise keep the current value.
 * - Compute updatedAt according to BR-2: updatedAt := max(current.updatedAt, now).
 *
 * Notes:
 * - Validation is out of scope (handled by UpdateEntryValidator).
 * - No exceptions are thrown here; this class is a pure normalizer.
 *
 * @return array{
 *   id: string,
 *   title: string,
 *   body: string,
 *   date: string,
 *   createdAt: string,
 *   updatedAt: string
 * }
 */
final class UpdateEntryInputNormalizer
{
    /**
     * Build normalized parameters for Entry::fromArray().
     *
     * @param UpdateEntryRequestInterface $request DTO carrying id and optional fields.
     * @param Entry                       $current Current persisted Entry snapshot.
     * @return array{id:string,title:string,body:string,date:string,createdAt:string,updatedAt:string}
     */
    public static function normalize(UpdateEntryRequestInterface $request, Entry $current): array
    {
        // Immutable fields
        $id        = $current->getId();
        $createdAt = $current->getCreatedAt();

        $currentTitle = $current->getTitle();
        $currentBody  = $current->getBody();
        $currentDate  = $current->getDate();

        // Optional fields (sanitized earlier at transport level; no trimming here)
        $newTitle = $request->getTitle();
        $newBody  = $request->getBody();
        $newDate  = $request->getDate();

        $titleProvided = $newTitle !== null;
        $bodyProvided  = $newBody  !== null;
        $dateProvided  = $newDate  !== null;

        $title = $titleProvided ? $newTitle : $currentTitle;
        $body  = $bodyProvided  ? $newBody  : $currentBody;
        $date  = $dateProvided  ? $newDate  : $currentDate;

        // BR-2: updatedAt := max(previous.updatedAt, now)
        $newUpdatedAt = Clock::now();
        $curUpdatedAt = $current->getUpdatedAt();

        $updatedAt = DateService::maxIsoUtc($newUpdatedAt, $curUpdatedAt);

        // Assemble payload
        $payload = [
            'id'        => $id,
            'title'     => $title,
            'body'      => $body,
            'date'      => $date,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt
        ];

        return $payload;
    }
}
