<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries\Api;

use Daylog\Presentation\Http\HttpRequest;
use Daylog\Presentation\Http\ResponseCode;
use Daylog\Presentation\Views\ResponsePayload;

use Daylog\Presentation\Requests\Entries\GetEntry\GetEntryRequestFactory;
use Daylog\Configuration\Providers\Entries\GetEntryProvider;
// use Daylog\Presentation\Http\JsonResponder;
use Daylog\Application\DTO\Entries\GetEntry\GetEntryRequestInterface;
use Daylog\Application\UseCases\Entries\GetEntry\GetEntryInterface;
use Daylog\Application\Responses\UseCaseResponseInterface;


use Daylog\Presentation\Controllers\BaseController;

use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Exceptions\TransportValidationException;
use Throwable;

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
    // public function show(): void
    // {
    //     $request = HttpRequest::params();

    //     /** @var GetEntryRequestInterface $request */
    //     $request = GetEntryRequestFactory::fromArray($request);

    //     /** @var GetEntryInterface $useCase */
    //     $useCase = GetEntryProvider::useCase();

    //     /** @var UseCaseResponseInterface $response */
    //     $response = $useCase->execute($request);

    //     // JsonResponder::emit($response);
    //     $payload = $response->toArray();
    //     var_dump($payload);
    //     exit;
    // }

    public function show(): void
    {
        try {
            $request = HttpRequest::params();
            $request = GetEntryRequestFactory::fromArray($request);
            $useCase = GetEntryProvider::useCase();
            
            $response = $useCase->execute($request);

            // UC completed without exceptions → success 200
            $payload = ResponsePayload::success()
                ->withStatus(200)
                ->withData($response->toArray());

        } catch (TransportValidationException $e) {
            $payload = ResponsePayload::failure()
                ->withStatus(400)
                ->withCode(ResponseCode::TRANSPORT_INVALID_INPUT)
                ->withError($e->getMessage());
        } catch (DomainValidationException $e) {
            $payload = ResponsePayload::failure()
                ->withStatus(422)
                ->withCode($e->getError());
        } catch (Throwable $e) {
            $payload = ResponsePayload::failure()
                ->withStatus(500)
                ->withCode(ResponseCode::UNEXPECTED_ERROR)
                ->withError($e->getMessage());
        }

        $this->response->setJson($payload);
    }    
}
