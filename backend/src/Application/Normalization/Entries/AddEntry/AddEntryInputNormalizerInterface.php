<?php
declare(strict_types=1);

namespace Daylog\Application\Normalization\Entries\AddEntry;

/**
 * Contract for UC-1 Add Entry input normalization.
 *
 * Purpose:
 * Provides a stable application-layer interface to transform raw
 * transport maps into a normalized shape expected by the request DTO.
 *
 * Mechanics:
 * - No business validation here (lengths, date format): validators handle ENTRY-BR-1..ENTRY-BR-2.
 * - A single time snapshot is taken to satisfy BR-2: createdAt === updatedAt on creation.
 * - Output shape is ready for Entry::fromArray() and repository save().
 *
 * @template TNormalized of array{
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
     * @param array<string,mixed> $input Raw map (e.g. parsed query/body).
     * @return TNormalized        Normalized map for constructing the request DTO.
     */
    public static function normalize(array $input): array;
}
     
