<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries\Api;

use Daylog\Presentation\Controllers\BaseController;
use Daylog\Presentation\Http\HttpRequest;
use Daylog\Presentation\Http\ResponseCode;
use Daylog\Presentation\Views\ResponsePayload;

use Daylog\Presentation\Requests\Entries\AddEntry\AddEntryRequestFactory;

use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Exceptions\TransportValidationException;

use Daylog\Configuration\Providers\Entries\AddEntryProvider;
use Throwable;

/**
 * AddEntryController (POST /api/entries)
 *
 * Purpose:
 * Handle the REST endpoint for creating a new entry (UC-1) with clean, framework-agnostic wiring.
 * Reads JSON body from the request, builds a typed DTO via a Presentation factory, resolves
 * the UC-1 use case via a Configuration provider, executes it, and emits standardized JSON.
 *
 * Mechanics:
 * - Extract raw JSON body (transport layer) via HttpRequest;
 * - Build AddEntryRequestInterface using AddEntryRequestFactory::fromArray() (transport validation only);
 * - Resolve AddEntryInterface via AddEntryProvider::useCase();
 * - Execute the use case and map known exceptions to 400/422, unexpected to 500;
 * - On success, return 200 with the use case response payload (project contract).
 */
final class AddEntryController extends BaseController
{
    /**
     * Create a new entry (UC-1).
     *
     * @return void
     */
    public function create(): void
    {
        try {
            $requestBody = HttpRequest::body();
            $request     = AddEntryRequestFactory::fromArray($requestBody);
            $useCase     = AddEntryProvider::useCase();

            $response = $useCase->execute($request);

            $payload = ResponsePayload::success()
                ->withStatus(200)
                ->withData($response->toArray());

        } catch (TransportValidationException $e) {
            $code    = 400;
            $error   = $e->getError();

            $payload = ResponsePayload::failure()
                ->withStatus($code)
                ->withCode($error);

        } catch (DomainValidationException $e) {
            $code    = 422;
            $error   = $e->getError();

            $payload = ResponsePayload::failure()
                ->withStatus($code)
                ->withCode($error);

        } catch (Throwable $e) {
            $code    = 500;
            $error   = ResponseCode::UNEXPECTED_ERROR;

            $payload = ResponsePayload::failure()
                ->withStatus($code)
                ->withCode($error);
        }

        $this->response->setJson($payload);
    }
}
