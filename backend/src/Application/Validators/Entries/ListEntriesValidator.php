<?php

declare(strict_types=1);

namespace Daylog\Application\Validators\Entries;

use DateTimeImmutable;
use Daylog\Application\DTO\Entries\ListEntriesRequestInterface;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Domain\Services\DateService;

/**
 * Validates ListEntries request against business rules.
 */
final class ListEntriesValidator implements ListEntriesValidatorInterface
{
    /** @var string[] */
    private const ALLOWED_SORT_FIELDS = ['date', 'createdAt', 'updatedAt'];

    /** @var string[] */
    private const ALLOWED_DIRECTIONS = ['ASC', 'DESC'];

    /**
     * @inheritDoc
     */
    public function validate(ListEntriesRequestInterface $req): void
    {
        $errors = [];

        $dateFrom  = $req->getDateFrom();
        $dateTo    = $req->getDateTo();
        $date      = $req->getDate();
        $page      = $req->getPage();
        $perPage   = $req->getPerPage();
        $sort      = $req->getSort();
        $direction = $req->getDirection();        

        // DateFrom rules
        if ($dateFrom !== null) {
            $isValid = DateService::isValidLocalDate($dateFrom);
            if ($isValid === false) {
                $errors[] = 'DATE_INVALID';
            }
        }

        // DateTo rules
        if ($dateTo !== null) {
            $isValid = DateService::isValidLocalDate($dateTo);
            if ($isValid === false) {
                $errors[] = 'DATE_INVALID';
            }
        }

        // Exact Date rules
        if ($date !== null) {
            $isValid = DateService::isValidLocalDate($date);
            if ($isValid === false) {
                $errors[] = 'DATE_INVALID';
            }
        }        

        // Date range rule
        if ($dateFrom !== null && $dateTo !== null) {
            $from = new DateTimeImmutable($dateFrom);
            $to   = new DateTimeImmutable($dateTo);

            if ($from > $to) {
                $errors[] = 'DATE_RANGE_INVALID';
            }
        }

        // Page rules
        if ($page < 1) {
            $errors[] = 'PAGE_INVALID';
        }

        // PerPage rules
        if ($perPage < 1 || $perPage > 100) {
            $errors[] = 'PER_PAGE_INVALID';
        }

        // Sort rules
        if (!in_array($sort, self::ALLOWED_SORT_FIELDS, true)) {
            $errors[] = 'SORT_INVALID';
        }

        // Direction rules
        if (!in_array($direction, self::ALLOWED_DIRECTIONS, true)) {
            $errors[] = 'DIRECTION_INVALID';
        }

        if ($errors !== []) {
            throw new DomainValidationException($errors);
        }
    }
}
