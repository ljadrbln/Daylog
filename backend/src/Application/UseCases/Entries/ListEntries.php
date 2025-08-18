<?php

declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries;

use Daylog\Application\DTO\Entries\ListEntriesRequest;
use Daylog\Application\DTO\Entries\ListEntriesResponse;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;

/**
 * UC-2 List Entries
 *
 * Use case for listing diary entries with optional filters, sorting, and pagination.
 * Currently a minimal implementation to satisfy unit tests compilation.
 */
final class ListEntries
{
    private EntryRepositoryInterface $repository;

    /**
     * Constructor.
     *
     * @param EntryRepositoryInterface $repository Repository for accessing entries.
     */
    public function __construct(EntryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Execute the use case.
     *
     * @param ListEntriesRequest $request Request parameters (filters, pagination, sorting).
     *
     * @return ListEntriesResponse
     */
    public function execute(ListEntriesRequest $request): ListEntriesResponse
    {
        $response = new ListEntriesResponse(
            items: [],
            page: 1,
            perPage: 10,
            total: 0,
            pagesCount: 0
        );

        return $response;
    }
}
