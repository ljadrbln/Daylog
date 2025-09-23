<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries\Api;

use Daylog\Presentation\Http\HttpRequest;
use Daylog\Presentation\Http\ResponseCode;
use Daylog\Presentation\Views\ResponsePayload;
use Daylog\Presentation\Controllers\BaseController;
use Daylog\Presentation\Requests\Entries\DeleteEntry\DeleteEntryRequestFactory;

use Daylog\Application\Exceptions\NotFoundException;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Exceptions\TransportValidationException;

use Daylog\Configuration\Providers\Entries\DeleteEntryProvider;
use Throwable;

/**
 * DeleteEntryController (DELETE /api/entries/:id)
 *
 * Purpose:
 * Handle the REST endpoint for deleting a single entry by id (UC-4) without framework coupling.
 * Reads path/query parameters from superglobals, builds a typed DTO via Presentation factory,
 * resolves UC-4 via Configuration provider, executes it, and emits standardized JSON.
 *
 * Mechanics:
 * - Extract raw request params ($_GET/$_POST merged by HttpRequest);
 * - Build DeleteEntryRequestInterface using factory (transport validation only);
 * - Resolve DeleteEntryInterface via DeleteEntryProvider::useCase();
 * - Execute use case and map known exceptions to 400/404/422, unexpected to 500.
 */
final class DeleteEntryController extends BaseController
{
    /**
     * Delete a single entry by id (UC-4).
     *
     * @return void
     */
    public function remove(): void
    {
        try {
            $params  = HttpRequest::params();
            $request = DeleteEntryRequestFactory::fromArray($params);
            $useCase = DeleteEntryProvider::useCase();

            $response = $useCase->execute($request);

            $code    = 200;
            $data    = $response->toArray();

            $payload = ResponsePayload::success()
                ->withStatus($code)
                ->withData($data);

        } catch (TransportValidationException $e) {
            $code    = 400;
            $error   = $e->getError();

            $payload = ResponsePayload::failure()
                ->withStatus($code)
                ->withCode($error);

        } catch (NotFoundException $e) {
            $code    = 404;
            $error   = ResponseCode::ENTRY_NOT_FOUND;

            $payload = ResponsePayload::failure()
                ->withStatus(404)
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
