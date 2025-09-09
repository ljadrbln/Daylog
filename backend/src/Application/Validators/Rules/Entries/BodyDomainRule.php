<?php
declare(strict_types=1);

namespace Daylog\Application\Validators\Rules\Entries;

use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\DTO\Entries\UpdateEntry\UpdateEntryRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Models\Entries\EntryConstraints;

/**
 * Domain rule for Entry body (UC-1 required, UC-5 optional).
 */
final class BodyDomainRule
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
        $body = $request->getBody();

        if ($body === '') {
            $message   = 'BODY_REQUIRED';
            $exception = new DomainValidationException($message);

            throw $exception;
        }

        $tooLong = mb_strlen($body) > EntryConstraints::BODY_MAX;
        if ($tooLong === true) {
            $message   = 'BODY_TOO_LONG';
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
        $body = $request->getBody();

        if ($body === null) {
            return;
        }

        self::assertValidRequired($request);
    }
}
