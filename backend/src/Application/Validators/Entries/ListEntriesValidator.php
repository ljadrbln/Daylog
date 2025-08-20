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
    /**
     * @inheritDoc
     */
    public function validate(ListEntriesRequestInterface $req): void
    {
        $errors = [];

        $dateFrom  = $req->getDateFrom();
        $dateTo    = $req->getDateTo();
        $page      = $req->getPage();
        $perPage   = $req->getPerPage();

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

        if ($errors !== []) {
            throw new DomainValidationException($errors);
        }
    }
}
