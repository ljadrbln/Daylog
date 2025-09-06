<?php

declare(strict_types=1);

namespace Daylog\Application\UseCases\Entries\ListEntries;

use Daylog\Application\UseCases\Entries\ListEntries\ListEntriesInterface;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesResponse;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesResponseInterface;
use Daylog\Application\Validators\Entries\ListEntries\ListEntriesValidatorInterface;
use Daylog\Application\Normalization\Entries\ListEntries\ListEntriesInputNormalizer;
use Daylog\Domain\Interfaces\Entries\EntryRepositoryInterface;
use Daylog\Domain\Models\Entries\ListEntriesCriteria;

/**
 * UC-2: List Entries (Application layer).
 *
 * Purpose:
 * Orchestrate the listing flow for entries:
 * - run business validation;
 * - normalize request DTO;
 * - translate Application DTO to Domain Criteria;
 * - delegate data retrieval to the repository;
 * - build a typed response DTO with items and pagination metadata.
 *
 * Notes:
 * - Sorting, filtering, and pagination are applied by the repository based on Domain Criteria.
 * - The use case remains thin and does not perform data-processing logic itself.
 */
final class ListEntries implements ListEntriesInterface
{
    /** @var EntryRepositoryInterface */
    private EntryRepositoryInterface $repository;

    /** @var ListEntriesValidatorInterface */
    private ListEntriesValidatorInterface $validator;

    /**
     * Constructor.
     *
     * @param EntryRepositoryInterface       $repository Repository abstraction for entries fetching.
     * @param ListEntriesValidatorInterface  $validator  Business validator for UC-2 parameters.
     */
    public function __construct(
        EntryRepositoryInterface $repository,
        ListEntriesValidatorInterface $validator
    ) {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Execute UC-2 with a normalized request DTO.
     *
     * Mechanics:
     * 1) Run business validation (date format, query length, etc.).
     * 2) Normalize raw params into canonical types/ranges.
     * 3) Map request to Domain Criteria (primary + stable secondary sort inside criteria).
     * 4) Fetch a page from repository.
     * 5) Build and return a typed response DTO.
     *
     * @param ListEntriesRequestInterface   $request Normalized request parameters (filters, paging, sort).
     * @return ListEntriesResponseInterface Response with items and pagination meta.
     */
    public function execute(ListEntriesRequestInterface $request): ListEntriesResponseInterface
    {
        $this->validator->validate($request);

        // Normalize list params
        $params   = ListEntriesInputNormalizer::normalize($request);
        $criteria = ListEntriesCriteria::fromArray($params);
        
        // Fetch page
        $pageData = $this->repository->findByCriteria($criteria);

        // Response DTO
        $response = ListEntriesResponse::fromArray($pageData);

        return $response;
    }
}