<?php
declare(strict_types=1);

namespace Daylog\Application\DTO\Common;

/**
 * Base contract for all Use Case response DTOs.
 *
 * Purpose:
 * Provide a unified serialization method for Application → Presentation handoff.
 * Every use case response implements this contract so that Presentation can
 * safely call toArray() and receive a predictable associative payload.
 *
 * Mechanics:
 * - Implementations define the exact shape of the payload via PHPStan @implements.
 * - Implementations MUST expose toArray(), returning only scalars/arrays
 *   (no domain entities or transport details).
 *
 * @template TPayload of array<string, mixed>
 */
interface UseCaseResponseInterface
{
    /**
     * Convert the response DTO into a normalized associative payload.
     *
     * @return TPayload Normalized payload ready for Presentation.
     */
    public function toArray(): array;
}
