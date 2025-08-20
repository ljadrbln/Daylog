<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Entries;

use Daylog\Domain\Services\DateService;
use Daylog\Domain\Models\Entries\EntryConstraints;
use Daylog\Application\DTO\Entries\AddEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Entries\AddEntryValidatorInterface;

/**
 * Validates business rules for AddEntry request.
 *
 * Rules:
 *  - title: not empty, max length TITLE_MAX
 *  - body:  not empty, max length BODY_MAX
 *  - date:  ISO format YYYY-MM-DD and valid calendar date
 *
 * Transport-level checks (types/presence) are NOT performed here.
 */
final class AddEntryValidator implements AddEntryValidatorInterface
{
    /**
     * Validate domain rules. Aggregates all errors and throws once.
     *
     * @param AddEntryRequestInterface $req
     * @return void
     *
     * @throws DomainValidationException
     */
    public function validate(AddEntryRequestInterface $req): void
    {
        $errors = [];

        $title = $req->getTitle();
        $body  = $req->getBody();
        $date  = $req->getDate();

        // Title rules
        if ($title === '') {
            $errors[] = 'TITLE_EMPTY';
        } elseif (mb_strlen($title) > EntryConstraints::TITLE_MAX) {
            $errors[] = 'TITLE_TOO_LONG';
        }

        // Body rules
        if ($body === '') {
            $errors[] = 'BODY_EMPTY';
        } elseif (mb_strlen($body) > EntryConstraints::BODY_MAX) {
            $errors[] = 'BODY_TOO_LONG';
        }

        // Date rules (strict ISO local date, via Domain service)
        $isValidDate = DateService::isValidLocalDate($date);
        if ($isValidDate === false) {
            $errors[] = 'DATE_INVALID';
        }

        if ($errors !== []) {
            throw new DomainValidationException($errors);
        }
    }
}
