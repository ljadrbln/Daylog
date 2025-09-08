<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries\AddEntry;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Presentation\Requests\Entries\AddEntry\AddEntrySanitizer;
use Daylog\Application\Exceptions\TransportValidationException;

use Daylog\Presentation\Requests\Rules\Entries\BodyTransportRule;
use Daylog\Presentation\Requests\Rules\Entries\DateTransportRule;
use Daylog\Presentation\Requests\Rules\Entries\TitleTransportRule;

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
     * @param array{
     *     title:string,
     *     body:string,
     *     date:string
     * } $params Raw transport map (e.g., $_GET or JSON).
     * 
     * @return AddEntryRequestInterface Typed request DTO for UC-1.
     *
     * @throws TransportValidationException When any known field is missing or has invalid type.
     */
    public static function fromArray(array $params): AddEntryRequestInterface
    {
        TitleTransportRule::assertValidRequired($params);
        BodyTransportRule::assertValidRequired($params);
        DateTransportRule::assertValidRequired($params);

        $params  = AddEntrySanitizer::sanitize($params);
        $request = AddEntryRequest::fromArray($params);

        return $request;
    }
}
