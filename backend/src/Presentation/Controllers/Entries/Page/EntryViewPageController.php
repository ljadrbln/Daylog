<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries\Page;
use Daylog\Presentation\Controllers\BaseController;
use Daylog\Presentation\Views\ResponsePayload;
use Daylog\Presentation\Http\HttpRequest;

/**
 * Controller for displaying the entry editing form (HTML).
 *
 * Purpose:
 * - Provide template and script information for the edit entry page.
 * - Wrap page data into ResponsePayload for consistent rendering.
 */
final class EntryViewPageController extends BaseController
{
    /**
     * Show the entry edit page.
     *
     * @return void
     */
    public function show(): void
    {
        $params = HttpRequest::params();

        $data = [
            'template' => 'entry-view.html',
            'script'   => 'entry-view-vanilla.js',
            'entryId'  => $params['id']
        ];

        $payload = ResponsePayload::success()
            ->withStatus(200)
            ->withData($data);

        $this->response->setHtml($payload);
    }
}


