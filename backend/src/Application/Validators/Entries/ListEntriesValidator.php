<?php

declare(strict_types=1);

namespace Daylog\Application\Validators\Entries;

use DateTimeImmutable;
use Daylog\Application\DTO\Entries\ListEntriesRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\DateService;

/**
 * Validates business rules for ListEntries request.
 *
 * Rules:
 *  - page:      must be >= 1
 *  - perPage:   must be within allowed bounds (e.g. 1–100)
 *  - sort:      must be one of ['date', 'createdAt', 'updatedAt']
 *  - direction: must be 'ASC' or 'DESC'
 *  - dateFrom, dateTo, date: must be strict ISO format YYYY-MM-DD
 *                             and valid calendar dates
 *  - query:     optional string filter (trimmed, case-insensitive)
 *
 * Transport-level checks (types/presence) are NOT performed here.
 * Only business-rule validation is applied.
 *
 * References:
 *  - UC-2 List Entries (see docs/UC-2-ListEntries.md)
 *  - Business Rules BR-3, BR-4, BR-6
 */
final class ListEntriesValidator implements ListEntriesValidatorInterface
{
    /** @var int */
    private const PER_PAGE_MIN = 1;
    /** @var int */
    private const PER_PAGE_MAX = 100;

    /** @var string[] */
    private const ALLOWED_SORT_FIELDS = ['date', 'createdAt', 'updatedAt'];

    /** @var string[] */
    private const ALLOWED_DIRECTIONS = ['ASC', 'DESC'];

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
        $errors = $this->validatePage($req, $errors);
        $errors = $this->validatePerPage($req, $errors);
        $errors = $this->validateSort($req, $errors);
        $errors = $this->validateDirection($req, $errors);

        if ($errors !== []) {
            throw new DomainValidationException($errors);
        }
    }

    /**
     * Validate dateFrom, dateTo, and exact date.
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

        if ($dateFrom !== null) {
            $isValid = DateService::isValidLocalDate($dateFrom);
            if ($isValid === false) {
                $errors[] = 'DATE_FROM_INVALID';
            }
        }

        if ($dateTo !== null) {
            $isValid = DateService::isValidLocalDate($dateTo);
            if ($isValid === false) {
                $errors[] = 'DATE_TO_INVALID';
            }
        }

        if ($date !== null) {
            $isValid = DateService::isValidLocalDate($date);
            if ($isValid === false) {
                $errors[] = 'DATE_INVALID';
            }
        }

        return $errors;
    }

    /**
     * Validate that dateFrom <= dateTo when both are present and valid.
     *
     * @param ListEntriesRequestInterface $req
     * @param string[]                    $errors
     * @return string[]
     */
    private function validateDateRange(ListEntriesRequestInterface $req, array $errors): array
    {
        $dateFrom = $req->getDateFrom();
        $dateTo   = $req->getDateTo();

        if ($dateFrom !== null && $dateTo !== null) {
            $from = new DateTimeImmutable($dateFrom);
            $to   = new DateTimeImmutable($dateTo);

            if ($from > $to) {
                $errors[] = 'DATE_RANGE_INVALID';
            }
        }

        return $errors;
    }

    /**
     * Validate page.
     *
     * @param ListEntriesRequestInterface $req
     * @param string[]                    $errors
     * @return string[]
     */
    private function validatePage(ListEntriesRequestInterface $req, array $errors): array
    {
        $page = $req->getPage();

        if ($page < 1) {
            $errors[] = 'PAGE_INVALID';
        }

        return $errors;
    }

    /**
     * Validate perPage.
     *
     * @param ListEntriesRequestInterface $req
     * @param string[]                    $errors
     * @return string[]
     */
    private function validatePerPage(ListEntriesRequestInterface $req, array $errors): array
    {
        $perPage = $req->getPerPage();

        $isTooSmall = ($perPage < self::PER_PAGE_MIN);
        $isTooBig   = ($perPage > self::PER_PAGE_MAX);

        if ($isTooSmall || $isTooBig) {
            $errors[] = 'PER_PAGE_INVALID';
        }

        return $errors;
    }

    /**
     * Validate sort field.
     *
     * @param ListEntriesRequestInterface $req
     * @param string[]                    $errors
     * @return string[]
     */
    private function validateSort(ListEntriesRequestInterface $req, array $errors): array
    {
        $sort = $req->getSort();

        $allowed   = self::ALLOWED_SORT_FIELDS;
        $isAllowed = in_array($sort, $allowed, true);

        if ($isAllowed === false) {
            $errors[] = 'SORT_INVALID';
        }

        return $errors;
    }

    /**
     * Validate direction.
     *
     * @param ListEntriesRequestInterface $req
     * @param string[]                    $errors
     * @return string[]
     */
    private function validateDirection(ListEntriesRequestInterface $req, array $errors): array
    {
        $direction = $req->getDirection();

        $allowed   = self::ALLOWED_DIRECTIONS;
        $isAllowed = in_array($direction, $allowed, true);

        if ($isAllowed === false) {
            $errors[] = 'DIRECTION_INVALID';
        }

        return $errors;
    }    
}
