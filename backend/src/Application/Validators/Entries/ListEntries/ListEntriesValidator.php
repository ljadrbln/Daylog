<?php

declare(strict_types=1);

namespace Daylog\Application\Validators\Entries\ListEntries;

use DateTimeImmutable;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\DateService;
use Daylog\Domain\Models\Entries\ListEntriesConstraints;

/**
 * Validates business rules for ListEntries request.
 *
 * Scope:
 * - Dates: strict format (YYYY-MM-DD) and calendar validity (BR-6).
 * - Date range: dateFrom <= dateTo when both present and valid.
 * - Query: length limit (0..QUERY_MAX) after normalization.
 *
 * Notes:
 * - Pagination and sorting bounds/defaults are enforced by the normalizer
 *   (clamping and fallbacks), so they are not re-validated here per SRP.
 */
final class ListEntriesValidator implements ListEntriesValidatorInterface
{
    /**
     * Validate domain rules. Aggregates all errors and throws once.
     *
     * @param ListEntriesRequestInterface $req
     * @return void
     *
     * @throws DomainValidationException
     */
    public function validate(ListEntriesRequestInterface $req): void
    {
        $this->validateDates($req);
        $this->validateDateRange($req);
        $this->validateQuery($req);
    }

    /**
     * Validate dateFrom, dateTo, and exact date (strict YYYY-MM-DD and calendar).
     *
     * @param ListEntriesRequestInterface $request
     * @return void
     */
    private function validateDates(ListEntriesRequestInterface $request): void
    {
        $dateFrom = $request->getDateFrom();
        $dateTo   = $request->getDateTo();
        $date     = $request->getDate();

        if (!is_null($dateFrom)) {
            $isValidFrom = DateService::isValidLocalDate($dateFrom);
            if ($isValidFrom === false) {
                $errorCode = 'DATE_INVALID'; 
                $exception = new DomainValidationException($errorCode);

                throw $exception;
            }
        }

        if (!is_null($dateTo)) {
            $isValidTo = DateService::isValidLocalDate($dateTo);
            if ($isValidTo === false) {
                $errorCode = 'DATE_INVALID'; 
                $exception = new DomainValidationException($errorCode);

                throw $exception;
            }
        }

        if (!is_null($date)) {
            $isValidExact = DateService::isValidLocalDate($date);
            if ($isValidExact === false) {
                $errorCode = 'DATE_INVALID'; 
                $exception = new DomainValidationException($errorCode);

                throw $exception;
            }
        }
    }

    /**
     * Validate that dateFrom <= dateTo when both are present and valid.
     *
     * Mechanics:
     * - Only compares when both dates are non-null and valid by DateService.
     *
     * @param ListEntriesRequestInterface $request
     * @return void
     */
    private function validateDateRange(ListEntriesRequestInterface $request): void
    {
        $dateFrom = $request->getDateFrom();
        $dateTo   = $request->getDateTo();

        $bothPresent = !is_null($dateFrom) && !is_null($dateTo);
        if ($bothPresent === false) {
            return;
        }

        $from = new DateTimeImmutable($dateFrom);
        $to   = new DateTimeImmutable($dateTo);

        if ($from > $to) {
            $errorCode = 'DATE_RANGE_INVALID'; 
            $exception = new DomainValidationException($errorCode);

            throw $exception;
        }
    }

    /**
     * Validate query length.
     *
     * Mechanics:
     * - Null or empty string is allowed (no error).
     * - Non-empty queries must not exceed QUERY_MAX characters.
     *
     * @param ListEntriesRequestInterface $request
     * @return void
     */
    private function validateQuery(ListEntriesRequestInterface $request): void
    {
        $query = $request->getQuery();

        if (is_null($query) || $query === '') {
            return;
        }

        $length = mb_strlen($query);
        $max    = ListEntriesConstraints::QUERY_MAX;

        if ($length > $max) {
            $errorCode = 'QUERY_TOO_LONG'; 
            $exception = new DomainValidationException($errorCode);

            throw $exception;            
        }
    }
}
