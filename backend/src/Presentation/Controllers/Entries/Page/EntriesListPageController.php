<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries\Page;
use Daylog\Presentation\Controllers\BaseController;
use Daylog\Presentation\Views\ResponsePayload;

/**
 * Controller for displaying the entries list page (HTML).
 */
final class EntriesListPageController extends BaseController
{
    /**
     * Show the entries list page.
     *
     * @return void
     */
    public function show(): void
    {
        $data = [
            'template' => 'list.html',
            'script'   => 'list.js',
        ];

        $payload = ResponsePayload::success()
            ->withStatus(200)
            ->withData($data);

        $this->response->setHtml($payload);
    }
}
