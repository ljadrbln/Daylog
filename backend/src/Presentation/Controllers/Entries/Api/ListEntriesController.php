<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries\Api;

use Daylog\Presentation\Controllers\BaseController;
use Daylog\Presentation\Http\HttpRequest;
use Daylog\Presentation\Http\ResponseCode;
use Daylog\Presentation\Views\ResponsePayload;

use Daylog\Presentation\Requests\Entries\ListEntries\ListEntriesRequestFactory;

use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\Exceptions\TransportValidationException;

use Daylog\Configuration\Providers\Entries\ListEntriesProvider;
use Throwable;

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
final class ListEntriesController extends BaseController
{
    /**
     * Show a paginated list of entries (UC-2).
     *
     * @return void
     */
    public function show(): void
    {
        try {
            $params  = HttpRequest::get();
            $request = ListEntriesRequestFactory::fromArray($params);
            $useCase = ListEntriesProvider::useCase();

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

        } catch (DomainValidationException $e) {
            $code    = 422;
            $error   = $e->getError();

            $payload = ResponsePayload::failure()
                ->withStatus($code)
                ->withCode($error);

        } catch (Throwable $e) {
            $code    = 500;
            $error   = ResponseCode::UNEXPECTED_ERROR;
var_dump($e);exit;
            $payload = ResponsePayload::failure()
                ->withStatus($code)
                ->withCode($error);
        }

        $this->response->setJson($payload);
    }
}
