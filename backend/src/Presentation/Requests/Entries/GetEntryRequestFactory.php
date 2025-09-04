<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries;

use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequest;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Presentation\Requests\Entries\GetEntrySanitizer;
use Daylog\Application\Exceptions\TransportValidationException;

/**
 * Builds GetEntryRequest DTO from raw transport input (UC-3, fail-first).
 *
 * Purpose:
 * Validate transport-level shape for UC-3 Get Entry:
 * - 'id' must be present and must be a string (no business checks here).
 * On the first violation, throw TransportValidationException immediately.
 *
 * Mechanics:
 * - Validate presence and type of 'id'.
 * - Sanitize input (trimming etc.) via GetEntrySanitizer.
 * - Construct typed DTO via GetEntryRequest::fromArray().
 *
 * @see GetEntrySanitizer
 */
final class GetEntryRequestFactory
{
    /**
     * Build a typed request DTO from raw transport map.
     *
     * @param array{id:mixed} $params Raw transport map (e.g. $_GET or decoded JSON).
     * @return GetEntryRequestInterface Typed request DTO for UC-3.
     *
     * @throws TransportValidationException
     *         ID_REQUIRED   When 'id' key is missing or null.
     *         ID_NOT_STRING When 'id' exists but is not a string.
     */
    public static function fromArray(array $params): GetEntryRequestInterface
    {
        self::validateId($params);

        $params  = GetEntrySanitizer::sanitize($params);
        $request = GetEntryRequest::fromArray($params);

        return $request;
    }

    /**
     * Validate id: must be present (non-null) and string.
     *
     * @param array<string,mixed> $input
     * @return void
     */
    private static function validateId(array $input): void
    {
        $rawId = $input['id'] ?? null;

        if(is_null($rawId)) {
            $errorCode = 'ID_REQUIRED';
            $exception = new TransportValidationException($errorCode);

            throw $exception;
        }

        if (!is_string($rawId)) { 
            $errorCode = 'ID_NOT_STRING';
            $exception = new TransportValidationException($errorCode);

            throw $exception;
        }
    }
}
