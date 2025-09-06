<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries\DeleteEntry;

use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequest;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequestInterface;
use Daylog\Application\Exceptions\TransportValidationException;
use Daylog\Presentation\Requests\Rules\IdTransportRule;
use Daylog\Presentation\Requests\Entries\DeleteEntry\DeleteEntrySanitizer;

/**
 * Builds DeleteEntryRequest DTO from raw transport input (UC-DeleteEntry, fail-first).
 *
 * Purpose:
 * Validate the transport-level shape for Delete Entry:
 * - 'id' must be present and must be a string (no business checks here).
 * On the first violation, throw TransportValidationException immediately.
 *
 * Mechanics:
 * - Validate presence and type of 'id'.
 * - Sanitize input (trimming etc.) via DeleteEntrySanitizer.
 * - Construct a typed DTO via DeleteEntryRequest::fromArray().
 *
 * Notes:
 * This factory performs only transport checks (shape and primitive types).
 * Domain rules (UUID format, existence, etc.) are enforced by validators/use case.
 *
 * @see DeleteEntrySanitizer
 */
final class DeleteEntryRequestFactory
{
    /**
     * Build a typed request DTO from a raw transport map.
     *
     * @param array{id:mixed} $params Raw transport map (e.g. route params or decoded JSON).
     * @return DeleteEntryRequestInterface Typed request DTO for Delete Entry.
     *
     * @throws TransportValidationException
     *         ID_REQUIRED   When 'id' key is missing or null.
     *         ID_NOT_STRING When 'id' exists but is not a string.
     */
    public static function fromArray(array $params): DeleteEntryRequestInterface
    {
        IdTransportRule::assertValid($params);

        $sanitized = DeleteEntrySanitizer::sanitize($params);
        $request   = DeleteEntryRequest::fromArray($sanitized);

        return $request;
    }
}
