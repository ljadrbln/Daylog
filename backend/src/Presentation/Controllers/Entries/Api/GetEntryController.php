<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries\Api;

use Daylog\Presentation\Http\HttpRequest;
use Daylog\Presentation\Http\ResponseCode;
use Daylog\Presentation\Views\ResponsePayload;
use Daylog\Presentation\Controllers\BaseController;
use Daylog\Presentation\Requests\Entries\GetEntry\GetEntryRequestFactory;

use Daylog\Application\Exceptions\NotFoundException;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Exceptions\TransportValidationException;

use Daylog\Configuration\Providers\Entries\GetEntryProvider;
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
    public function get(): void
    {
        try {
            $params  = HttpRequest::params();
            $request = GetEntryRequestFactory::fromArray($params);
            $useCase = GetEntryProvider::useCase();
            
            $response = $useCase->execute($request);

            $code    = 200;
            $data    = $response->toArray();

            $payload = ResponsePayload::success()
                ->withStatus($code)
                ->withData($data);

        } catch (TransportValidationException $e) {
            $payload = ResponsePayload::failure()
                ->withStatus(400)
                ->withCode($e->getError());

        } catch (NotFoundException $e) {
            $payload = ResponsePayload::failure()
                ->withStatus(404)
                ->withCode(ResponseCode::ENTRY_NOT_FOUND);

        } catch (DomainValidationException $e) {
            $payload = ResponsePayload::failure()
                ->withStatus(422)
                ->withCode($e->getError());

        } catch (Throwable $e) {
            $payload = ResponsePayload::failure()
                ->withStatus(500)
                ->withCode(ResponseCode::UNEXPECTED_ERROR);
        }

        $this->response->setJson($payload);
    }    
}
