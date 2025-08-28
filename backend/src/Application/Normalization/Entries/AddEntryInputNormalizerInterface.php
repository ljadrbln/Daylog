<?php
declare(strict_types=1);

namespace Daylog\Application\Normalization\Entries;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;

/**
 * Contract for UC-1 Add Entry input normalization.
 *
 * Purpose:
 * Provides a stable application-layer interface that transforms an AddEntryRequestInterface
 * into a strict payload for domain construction and persistence. The normalizer trims
 * content fields and appends technical fields (id, createdAt, updatedAt).
 *
 * Mechanics:
 * - No business validation here (lengths, date format): validators handle ENTRY-BR-1..ENTRY-BR-2.
 * - A single time snapshot is taken to satisfy BR-2: createdAt === updatedAt on creation.
 * - Output shape is ready for Entry::fromArray() and repository save().
 *
 * @template TAddEntryPayload of array{
 *     id:string,
 *     title:string,
 *     body:string,
 *     date:string,
 *     createdAt:string,
 *     updatedAt:string
 * }
 */
interface AddEntryInputNormalizerInterface
{
    /**
     * Normalize Add Entry request into a strict payload.
     *
     * @param AddEntryRequestInterface  $request User input DTO (raw strings before validation).
     * @return TAddEntryPayload         Payload with trimmed fields and technical attributes.
     */
    public function normalize(AddEntryRequestInterface $request): array;
}
