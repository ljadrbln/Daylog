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
        $errors = [];

        $errors = $this->validateDates($req, $errors);
        $errors = $this->validateDateRange($req, $errors);
        $errors = $this->validateQuery($req, $errors);

        if ($errors !== []) {
            $exception = new DomainValidationException($errors);
            throw $exception;
        }
    }

    /**
     * Validate dateFrom, dateTo, and exact date (strict YYYY-MM-DD and calendar).
     *
     * @param ListEntriesRequestInterface $req
     * @param string[]                    $errors
     * @return string[]
     */
    private function validateDates(ListEntriesRequestInterface $req, array $errors): array
    {
        $dateFrom = $req->getDateFrom();
        $dateTo   = $req->getDateTo();
        $date     = $req->getDate();

        if (!is_null($dateFrom)) {
            $isValidFrom = DateService::isValidLocalDate($dateFrom);
            if ($isValidFrom === false) {
                $errors[] = 'DATE_INVALID';
            }
        }

        if (!is_null($dateTo)) {
            $isValidTo = DateService::isValidLocalDate($dateTo);
            if ($isValidTo === false) {
                $errors[] = 'DATE_INVALID';
            }
        }

        if (!is_null($date)) {
            $isValidExact = DateService::isValidLocalDate($date);
            if ($isValidExact === false) {
                $errors[] = 'DATE_INVALID';
            }
        }

        return $errors;
    }

    /**
     * Validate that dateFrom <= dateTo when both are present and valid.
     *
     * Mechanics:
     * - Only compares when both dates are non-null and valid by DateService.
     *
     * @param ListEntriesRequestInterface $req
     * @param string[]                    $errors
     * @return string[]
     */
    private function validateDateRange(ListEntriesRequestInterface $req, array $errors): array
    {
        $dateFrom = $req->getDateFrom();
        $dateTo   = $req->getDateTo();

        $bothPresent = !is_null($dateFrom) && !is_null($dateTo);
        if ($bothPresent === false) {
            return $errors;
        }

        $from = new DateTimeImmutable($dateFrom);
        $to   = new DateTimeImmutable($dateTo);

        if ($from > $to) {
            $errors[] = 'DATE_RANGE_INVALID';
        }

        return $errors;
    }

    /**
     * Validate query length.
     *
     * Mechanics:
     * - Null or empty string is allowed (no error).
     * - Non-empty queries must not exceed QUERY_MAX characters.
     *
     * @param ListEntriesRequestInterface $req
     * @param string[]                    $errors
     * @return string[]
     */
    private function validateQuery(ListEntriesRequestInterface $req, array $errors): array
    {
        $query = $req->getQuery();

        if (is_null($query) || $query === '') {
            return $errors;
        }

        $length = mb_strlen($query);
        $max    = ListEntriesConstraints::QUERY_MAX;

        if ($length > $max) {
            $errors[] = 'QUERY_TOO_LONG';
        }

        return $errors;
    }
}
