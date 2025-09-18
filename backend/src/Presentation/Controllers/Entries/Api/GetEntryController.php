<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries\Api;

use Daylog\Presentation\Requests\Entries\GetEntry\GetEntryRequestFactory;
use Daylog\Configuration\Providers\Entries\GetEntryProvider;
// use Daylog\Presentation\Http\JsonResponder;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Application\UseCases\Entries\GetEntry\GetEntryInterface;
use Daylog\Application\Responses\UseCaseResponseInterface;

use Daylog\Presentation\Controllers\BaseController;

/**
 * GetEntryController (GET /api/entries/798637ef-9aec-4ad6-8c71-daeaef927c5b
 *
 * Purpose:
 * Handle the REST endpoint for fetching a single entry by id (UC-3) without framework coupling.
 * Reads query parameters from superglobals, builds a typed DTO via Presentation factory,
 * resolves UC-3 via Configuration provider, executes it, and emits standardized JSON.
 *
 * Mechanics:
 * - Extract raw query from superglobals ($_GET);
 * - Build GetEntryRequestInterface using factory (no business validation here);
 * - Resolve GetEntryInterface via GetEntryProvider::useCase();
 * - Execute use case and pass UseCaseResponse to JsonResponder (stubbed here via var_dump).
 */
final class GetEntryController extends BaseController
{
    /**
     * Show a single entry by id (UC-3).
     *
     * @return void
     */
    public function show(): void
    {
        $query = $_GET;

        /** @var GetEntryRequestInterface $request */
        $request = GetEntryRequestFactory::fromArray($query);

        /** @var GetEntryInterface $useCase */
        $useCase = GetEntryProvider::useCase();

        /** @var UseCaseResponseInterface $response */
        $response = $useCase->execute($request);

        // JsonResponder::emit($response);
        $payload = $response->toArray();
        var_dump($payload);
        exit;
    }
}
