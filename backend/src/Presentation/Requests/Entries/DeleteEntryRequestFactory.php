<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries;

use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequest;
use Daylog\Application\DTO\Entries\DeleteEntry\DeleteEntryRequestInterface;
use Daylog\Application\Exceptions\TransportValidationException;

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
        self::validateId($params);

        $sanitized = DeleteEntrySanitizer::sanitize($params);
        $request   = DeleteEntryRequest::fromArray($sanitized);

        return $request;
    }

    /**
     * Validate 'id' presence (non-null) and type (string).
     *
     * @param array<string,mixed> $input Raw input to check.
     * @return void
     *
     * @throws TransportValidationException
     */
    private static function validateId(array $input): void
    {
        $rawId = $input['id'] ?? null;

        $isNull = is_null($rawId);
        if ($isNull) {
            $errorCode = 'ID_REQUIRED';
            $exception = new TransportValidationException($errorCode);
            
            throw $exception;
        }

        $isString = is_string($rawId);
        if (!$isString) {
            $errorCode = 'ID_NOT_STRING';
            $exception = new TransportValidationException($errorCode);

            throw $exception;
        }
    }
}
