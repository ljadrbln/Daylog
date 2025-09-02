<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries;

use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequest;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Presentation\Requests\Entries\GetEntrySanitizer;
use Daylog\Application\Exceptions\TransportValidationException;

/**
 * Builds GetEntryRequest DTO from raw transport input.
 *
 * Purpose:
 * - Perform transport-level type checks (must be string or null) and raise TransportValidationException on violations.
 * - Construct a typed DTO using explicit local variables (no inline literals).
 *
 * Notes:
 * - No business validation is performed here.
 * - This factory only ensures type safety for UC-3 Get Entry.
 */

final class GetEntryRequestFactory
{
    /**
     * Builds GetEntryRequest DTO from raw transport input.
     *
     * Purpose:
     * - Perform transport-level presence & type checks (must exist and be string) and raise TransportValidationException on violations.
     * - Construct a typed DTO using explicit local variables (no inline literals).
     *
     * Notes:
     * - No business validation is performed here.
     * - This factory only ensures transport correctness for UC-3 Get Entry.
     *
     * @param array{
     *     id:string
     * } $params Raw transport map (e.g., $_GET or JSON).
     * 
     * @return GetEntryRequestInterface Typed request DTO for UC-1.
     *
     * @throws TransportValidationException When any known field is missing or has invalid type.
     */
    public static function fromArray(array $params): GetEntryRequestInterface
    {
        $errors = [];

        $errors = self::validateId($params, $errors);

        if ($errors !== []) {
            throw new TransportValidationException($errors);
        };

        $params  = GetEntrySanitizer::sanitize($params);
        $request = GetEntryRequest::fromArray($params);

        return $request;
    }

    /**
     * Validate title: must be present (non-null) and string.
     *
     * @param array<string,mixed> $input
     * @param string[]            $errors
     * @return string[]
     */
    private static function validateId(array $input, array $errors): array
    {
        $rawId = $input['id'] ?? null;

        if (!is_string($rawId)) { 
            $errors[] = 'ID_MUST_BE_STRING'; 
        }

        return $errors;
    }
}
