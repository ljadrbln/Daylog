<?php
declare(strict_types=1);

namespace Daylog\Application\Normalization\Entries;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Domain\Services\Clock;

/**
 * UC-1 input normalizer (GREEN).
 *
 * Purpose:
 * Transform AddEntryRequestInterface into a strict payload for Entry::fromArray() and persistence.
 * Trims content fields and appends technical attributes (id, createdAt, updatedAt).
 *
 * Mechanics:
 * - No business validation here (BR-1..BR-3, BR-6 are handled by validators).
 * - Takes a single time snapshot to satisfy BR-4: createdAt === updatedAt on creation.
 *
 * @param AddEntryRequestInterface $request Raw user input DTO (strings).
 *
 * @return array{
 *     id:string,
 *     title:string,
 *     body:string,
 *     date:string,
 *     createdAt:string,
 *     updatedAt:string
 * }
 */
final class AddEntryInputNormalizer
{
    /**
     * Normalize Add Entry request into a strict payload.
     * 
     * @param array{
     *     title:string,
     *     body:string,
     *     date:string
     * } $input Raw transport map (e.g., $_GET or JSON).
     * 
     * @return array{
     *     id:string,
     *     title:string,
     *     body:string,
     *     date:string,
     *     createdAt:string,
     *     updatedAt:string
     * }
     */
    public function normalize(array $input): array
    {
        // Trim input fields
        $title = $this->normalizeTitle($input);
        $body  = $this->normalizeBody($input);
        $date  = $this->normalizeDate($input);

        // Technical fields (Domain-level services; no Infrastructure calls here)
        $id  = UuidGenerator::generate();
        $now = Clock::now();

        // Assemble payload
        $payload = [
            'id'        => $id,
            'title'     => $title,
            'body'      => $body,
            'date'      => $date,
            'createdAt' => $now,
            'updatedAt' => $now,
        ];

        return $payload;
    }

    /**
     * Normalize title: trim entry title.
     *
     * @param array{
     *     title:string,
     *     body:string,
     *     date:string
     * } $input Raw transport map (e.g., $_GET or JSON).
     * 
     * @return string
     */
    private function normalizeTitle(array $input): string
    {
        $title = $input['title'];
        $title = trim($title);

        return $title;
    }

    /**
     * Normalize body: trim entry body.
     *
     * @param array{
     *     title:string,
     *     body:string,
     *     date:string
     * } $input Raw transport map (e.g., $_GET or JSON).
     * 
     * @return string
     */
    private function normalizeBody(array $input): string
    {
        $body = $input['body'];
        $body = trim($body);

        return $body;
    }

    /**
     * Normalize date: trim entry date.
     *
     * @param array{
     *     title:string,
     *     body:string,
     *     date:string
     * } $input Raw transport map (e.g., $_GET or JSON).
     * 
     * @return string
     */
    private function normalizeDate(array $input): string
    {
        $date = $input['date'];
        $date = trim($date);

        return $date;
    }    
}
