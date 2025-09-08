<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries\UpdateEntry;

use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequest;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Presentation\Requests\Entries\UpdateEntry\UpdateEntrySanitizer;
use Daylog\Application\Exceptions\TransportValidationException;

use Daylog\Presentation\Requests\Rules\Common\IdTransportRule;
use Daylog\Presentation\Requests\Rules\Entries\BodyTransportRule;
use Daylog\Presentation\Requests\Rules\Entries\DateTransportRule;
use Daylog\Presentation\Requests\Rules\Entries\TitleTransportRule;

/**
 * Builds UpdateEntryRequest DTO from raw transport input.
 *
 * Purpose:
 * - Perform transport-level type checks (must be string or null) and raise TransportValidationException on violations.
 * - Construct a typed DTO using explicit local variables (no inline literals).
 *
 * Notes:
 * - No business validation is performed here (length limits, trimming, date format).
 * - This factory only ensures type safety for UC-1 Add Entry.
 */

final class UpdateEntryRequestFactory
{
    /**
     * Builds UpdateEntryRequest DTO from raw transport input.
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
     *     id:string,
     *     title?:string,
     *     body?:string,
     *     date?:string
     * } $params Raw transport map (e.g., $_GET or JSON).
     * 
     * @return UpdateEntryRequestInterface Typed request DTO for UC-1.
     *
     * @throws TransportValidationException When any known field is missing or has invalid type.
     */
    public static function fromArray(array $params): UpdateEntryRequestInterface
    {
        IdTransportRule::assertValidRequired($params);

        TitleTransportRule::assertValidOptional($params);
        BodyTransportRule::assertValidOptional($params);
        DateTransportRule::assertValidOptional($params);
        
        $params  = UpdateEntrySanitizer::sanitize($params);
        $request = UpdateEntryRequest::fromArray($params);

        return $request;
    }
}
