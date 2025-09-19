<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries\Api;

use Daylog\Presentation\Controllers\BaseController;
use Daylog\Presentation\Http\HttpRequest;
use Daylog\Presentation\Http\ResponseCode;
use Daylog\Presentation\Views\ResponsePayload;

use Daylog\Presentation\Requests\Entries\UpdateEntry\UpdateEntryRequestFactory;

use Daylog\Application\Exceptions\NotFoundException;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Exceptions\TransportValidationException;

use Daylog\Configuration\Providers\Entries\UpdateEntryProvider;
use Throwable;

/**
 * UpdateEntryController (PUT /api/entries/{id})
 *
 * Purpose:
 * Handle the REST endpoint for updating an existing entry (UC-5) with clean, framework-agnostic wiring.
 * Reads JSON body from the request, builds a typed DTO via a Presentation factory, resolves
 * the UC-5 use case via a Configuration provider, executes it, and emits standardized JSON.
 *
 * Mechanics:
 * - Extract raw JSON body and route parameters via HttpRequest;
 * - Build UpdateEntryRequestInterface using UpdateEntryRequestFactory::fromArray() (transport validation only);
 * - Resolve UpdateEntryInterface via UpdateEntryProvider::useCase();
 * - Execute the use case and map known exceptions to 400/422, unexpected to 500;
 * - On success, return 200 with the use case response payload (project contract).
 */
final class UpdateEntryController extends BaseController
{
    /**
     * Update an existing entry (UC-5).
     *
     * @return void
     */
    public function update(): void
    {
        try {
            $requestBody = HttpRequest::body();
            $routeParams = HttpRequest::params();

            // Merge id from route into body to satisfy DTO contract
            $requestBody['id'] = $routeParams['id'] ?? null;

            $request  = UpdateEntryRequestFactory::fromArray($requestBody);
            $useCase  = UpdateEntryProvider::useCase();
            $response = $useCase->execute($request);
            
            $code    = 200;
            $data    = $response->toArray();
            
            $payload = ResponsePayload::success()
                ->withStatus(200)
                ->withData($data);

        } catch (TransportValidationException $e) {
            $code  = 400;
            $error = $e->getError();

            $payload = ResponsePayload::failure()
                ->withStatus($code)
                ->withCode($error);

        } catch (NotFoundException $e) {
            $payload = ResponsePayload::failure()
                ->withStatus(404)
                ->withCode(ResponseCode::ENTRY_NOT_FOUND);

        } catch (DomainValidationException $e) {
            $code  = 422;
            $error = $e->getError();

            $payload = ResponsePayload::failure()
                ->withStatus($code)
                ->withCode($error);

        } catch (Throwable $e) {
            $code  = 500;
            $error = ResponseCode::UNEXPECTED_ERROR;

            $payload = ResponsePayload::failure()
                ->withStatus($code)
                ->withCode($error);
        }

        $this->response->setJson($payload);
    }
}
