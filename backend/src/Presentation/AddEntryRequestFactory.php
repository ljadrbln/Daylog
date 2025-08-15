<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests;

use Daylog\Application\DTO\Entries\AddEntryRequestInterface;

final class AddEntryRequestFactory
{
    /**
     * Builds an AddEntryRequest DTO from raw HTTP input.
     * Performs only transport-level validation (types, required presence).
     *
     * @param array<string,mixed> $input
     * @return AddEntryRequestInterface
     */
    public function fromArray(array $input): AddEntryRequestInterface
    {
        // Not implemented yet — RED phase
    }
}
