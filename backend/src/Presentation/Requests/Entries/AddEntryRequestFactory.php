<?php
declare(strict_types=1);

namespace Daylog\Presentation\Requests\Entries;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\Exceptions\TransportValidationException;

/**
 * Builds AddEntryRequest DTO from raw HTTP input.
 * Performs transport-level validation (presence, types).
 */
final class AddEntryRequestFactory
{
    /**
     * @param array<string,mixed> $input
     * @return AddEntryRequestInterface
     *
     * @throws TransportValidationException
     */
    public function fromArray(array $input): AddEntryRequestInterface
    {
        $errors = [];

        $rawTitle = $input['title'] ?? null;
        $rawBody  = $input['body']  ?? null;
        $rawDate  = $input['date']  ?? null;

        if (!is_string($rawTitle)) {
            $errors[] = 'TITLE_MUST_BE_STRING';
        }

        if (!is_string($rawBody)) {
            $errors[] = 'BODY_MUST_BE_STRING';
        }

        if (!is_string($rawDate)) {
            $errors[] = 'DATE_MUST_BE_STRING';
        }

        if ($errors !== []) {
            throw new TransportValidationException($errors);
        }

        // Call DTO factory method
        $data = [
            'title' => $rawTitle,
            'body'  => $rawBody,
            'date'  => $rawDate,
        ];

        $request = AddEntryRequest::fromArray($data);
        
        return $request;
    }
}
