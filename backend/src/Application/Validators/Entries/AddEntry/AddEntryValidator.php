<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Entries\AddEntry;

use Daylog\Domain\Services\DateService;
use Daylog\Domain\Models\Entries\EntryConstraints;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Validators\Entries\AddEntry\AddEntryValidatorInterface;

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

        $errors = $this->validateTitle($req, $errors);
        $errors = $this->validateBody($req, $errors);
        $errors = $this->validateDate($req, $errors);

        if ($errors !== []) {
            throw new DomainValidationException($errors);
        }
    }

    /**
     * @param AddEntryRequestInterface $req
     * @param string[]                 $errors
     * @return string[]
     */
    private function validateTitle(AddEntryRequestInterface $req, array $errors): array
    {
        $title = $req->getTitle();

        if ($title === '') {
            $errors[] = 'TITLE_EMPTY';
        } elseif (mb_strlen($title) > EntryConstraints::TITLE_MAX) {
            $errors[] = 'TITLE_TOO_LONG';
        }

        return $errors;
    }

    /**
     * @param AddEntryRequestInterface $req
     * @param string[]                 $errors
     * @return string[]
     */
    private function validateBody(AddEntryRequestInterface $req, array $errors): array
    {
        $body = $req->getBody();

        if ($body === '') {
            $errors[] = 'BODY_EMPTY';
        } elseif (mb_strlen($body) > EntryConstraints::BODY_MAX) {
            $errors[] = 'BODY_TOO_LONG';
        }

        return $errors;
    }

    /**
     * @param AddEntryRequestInterface $req
     * @param string[]                 $errors
     * @return string[]
     */
    private function validateDate(AddEntryRequestInterface $req, array $errors): array
    {
        $date = $req->getDate();

        $isValid = DateService::isValidLocalDate($date);
        if ($isValid === false) {
            $errors[] = 'DATE_INVALID';
        }

        return $errors;
    }
}
