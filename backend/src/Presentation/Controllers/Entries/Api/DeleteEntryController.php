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
            $request = HttpRequest::params();
            $request = DeleteEntryRequestFactory::fromArray($request);
            $useCase = DeleteEntryProvider::useCase();

            $response = $useCase->execute($request);

            $payload = ResponsePayload::success()
                ->withStatus(200)
                ->withData($response->toArray());

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
