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
        $this->validateTitle($req);
        $this->validateBody($req);
        $this->validateDate($req);
    }

    /**
     * @param AddEntryRequestInterface $request
     * @return void
     */
    private function validateTitle(AddEntryRequestInterface $request): void
    {
        $title = $request->getTitle();

        if($title === '') {
            $errorCode = 'TITLE_EMPTY';
            $exception = new DomainValidationException($errorCode);

            throw $exception;
        }

        if (mb_strlen($title) > EntryConstraints::TITLE_MAX) {
            $errorCode = 'TITLE_TOO_LONG';
            $exception = new DomainValidationException($errorCode);

            throw $exception;            
        }
    }

    /**
     * @param AddEntryRequestInterface $request
     * @return void
     */
    private function validateBody(AddEntryRequestInterface $request): void
    {
        $body = $request->getBody();

        if($body === '') {
            $errorCode = 'BODY_EMPTY';
            $exception = new DomainValidationException($errorCode);

            throw $exception;
        }

        if (mb_strlen($body) > EntryConstraints::BODY_MAX) {
            $errorCode = 'BODY_TOO_LONG';
            $exception = new DomainValidationException($errorCode);

            throw $exception;
        }
    }

    /**
     * @param AddEntryRequestInterface $request
     * @return void
     */
    private function validateDate(AddEntryRequestInterface $request): void
    {
        $date = $request->getDate();

        $isValid = DateService::isValidLocalDate($date);
        if ($isValid === false) {
            $errorCode = 'DATE_INVALID';
            $exception = new DomainValidationException($errorCode);

            throw $exception;
        }
    }
}
