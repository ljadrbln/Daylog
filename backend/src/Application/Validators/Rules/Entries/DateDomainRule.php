<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Rules\Entries;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\DateService;

/**
 * Domain rule for Entry date (UC-1 required, UC-5 optional).
 *
 * Notes:
 * - UC-1: '' → DATE_REQUIRED; invalid → DATE_INVALID.
 * - UC-5: null → ok; '' → DATE_INVALID; invalid → DATE_INVALID.
 */
final class DateDomainRule
{
    /**
     * @param AddEntryRequestInterface|UpdateEntryRequestInterface $request
     * @return void
     *
     * @throws DomainValidationException
     */
    public static function assertValidRequired(
        AddEntryRequestInterface|UpdateEntryRequestInterface $request
    ): void {
        /** @var string $date */
        $date = $request->getDate();

        if ($date === '') {
            $message   = 'DATE_REQUIRED';
            $exception = new DomainValidationException($message);
            
            throw $exception;
        }

        $isValid = DateService::isValidLocalDate($date);
        if ($isValid === false) {
            $message   = 'DATE_INVALID';
            $exception = new DomainValidationException($message);

            throw $exception;
        }
    }

    /**
     * @param AddEntryRequestInterface|UpdateEntryRequestInterface $request
     * @return void
     *
     * @throws DomainValidationException
     */
    public static function assertValidOptional(
        AddEntryRequestInterface|UpdateEntryRequestInterface $request
    ): void {
        $date = $request->getBody();

        if ($date === null) {
            return;
        }

        self::assertValidRequired($request);
    }
}
