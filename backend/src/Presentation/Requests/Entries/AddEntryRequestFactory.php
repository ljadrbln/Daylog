<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\Exceptions\TransportValidationException;

/**
 * Builds AddEntryRequest DTO from raw transport input.
 *
 * Purpose:
 * - Perform transport-level type checks (must be string or null) and raise TransportValidationException on violations.
 * - Construct a typed DTO using explicit local variables (no inline literals).
 *
 * Notes:
 * - No business validation is performed here (length limits, trimming, date format).
 * - This factory only ensures type safety for UC-1 Add Entry.
 */

final class AddEntryRequestFactory
{
    /**
     * Builds AddEntryRequest DTO from raw transport input.
     *
     * Purpose:
     * - Perform transport-level presence & type checks (must exist and be string) and raise TransportValidationException on violations.
     * - Construct a typed DTO using explicit local variables (no inline literals).
     *
     * Notes:
     * - No business validation is performed here (length limits, trimming, date format).
     * - This factory only ensures transport correctness for UC-1 Add Entry.
     *
     * @param array<string,mixed> $input Raw transport map (e.g., JSON body).
     * @return AddEntryRequestInterface Typed request DTO for UC-1.
     *
     * @throws TransportValidationException When any known field is missing or has invalid type.
     */
    public static function fromArray(array $input): AddEntryRequestInterface
    {
        $errors = [];

        $errors = self::validateTitle($input, $errors);
        $errors = self::validateBody($input, $errors);
        $errors = self::validateDate($input, $errors);

        if ($errors !== []) {
            throw new TransportValidationException($errors);
        }

        $title = $input['title'];
        $body  = $input['body'];
        $date  = $input['date'];

        $data = [
            'title' => $title,
            'body'  => $body,
            'date'  => $date,
        ];

        $request = AddEntryRequest::fromArray($data);
        return $request;
    }

    /**
     * Validate title: must be present (non-null) and string.
     *
     * @param array<string,mixed> $input
     * @param string[]            $errors
     * @return string[]
     */
    private static function validateTitle(array $input, array $errors): array
    {
        $rawTitle = $input['title'] ?? null;

        if (!is_string($rawTitle)) { 
            $errors[] = 'TITLE_MUST_BE_STRING'; 
        }

        return $errors;
    }

    /**
     * Validate body: must be present (non-null) and string.
     *
     * @param array<string,mixed> $input
     * @param string[]            $errors
     * @return string[]
     */
    private static function validateBody(array $input, array $errors): array
    {
        $rawBody = $input['body'] ?? null;

        if (!is_string($rawBody)) { 
            $errors[] = 'BODY_MUST_BE_STRING'; 
        } 

        return $errors;
    }

    /**
     * Validate date: must be present (non-null) and string.
     *
     * @param array<string,mixed> $input
     * @param string[]            $errors
     * @return string[]
     */
    private static function validateDate(array $input, array $errors): array
    {
        $rawDate = $input['date'] ?? null; 

        if (!is_string($rawDate)) { 
            $errors[] = 'DATE_MUST_BE_STRING'; 
        }

        return $errors;
    }

}
