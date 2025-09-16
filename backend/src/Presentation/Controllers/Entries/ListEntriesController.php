<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries;

use Daylog\Presentation\Requests\Entries\ListEntries\ListEntriesRequestFactory;
use Daylog\Configuration\Providers\Entries\ListEntriesProvider;
//use Daylog\Presentation\Http\JsonResponder;
use Daylog\Application\DTO\Entries\ListEntries\ListEntriesRequestInterface;
use Daylog\Application\UseCases\Entries\ListEntries\ListEntriesInterface;
use Daylog\Application\Responses\UseCaseResponseInterface;

/**
 * ListEntriesController (GET /entries).
 *
 * Purpose:
 * Handle the REST endpoint for listing entries without framework coupling.
 * Reads query parameters, builds a typed DTO via Presentation factory,
 * resolves UC-2 via Configuration provider, executes it, and emits standardized JSON.
 *
 * Mechanics:
 * - Extract raw query from superglobals ($_GET);
 * - Build ListEntriesRequestInterface using factory (no business validation here);
 * - Resolve ListEntriesInterface via ListEntriesProvider::useCase();
 * - Execute use case and pass UseCaseResponse to JsonResponder.
 */
final class ListEntriesController
{
    /**
     * Show a paginated list of entries (UC-2).
     *
     * @return void
     */
    public function show(): void
    {
        /** @var array<string,mixed> $query */
        $query = [
            'page' => 1,
            'perPage' => 10,
            'sortField' => 'date',
            'sortDir'   => 'DESC'
        ];

        /** @var ListEntriesRequestInterface $request */
        $request = ListEntriesRequestFactory::fromArray($query);

        /** @var ListEntriesInterface $useCase */
        $useCase = ListEntriesProvider::useCase();

        /** @var UseCaseResponseInterface $response */
        $response = $useCase->execute($request);

        var_dump($response);exit;
    }
}
