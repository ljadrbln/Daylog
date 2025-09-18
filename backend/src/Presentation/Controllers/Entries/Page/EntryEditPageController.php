<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries\Page;
use Daylog\Presentation\Controllers\BaseController;
use Daylog\Presentation\Views\ResponsePayload;

/**
 * Controller for displaying the entry editing form (HTML).
 *
 * Purpose:
 * - Provide template and script information for the edit entry page.
 * - Wrap page data into ResponsePayload for consistent rendering.
 */
final class EntryEditPageController extends BaseController
{
    /**
     * Show the entry edit page.
     *
     * @return void
     */
    public function show(): void
    {
        $data = [
            'template' => 'edit.html',
            'script'   => 'edit.js',
        ];

        $payload = ResponsePayload::success()
            ->withStatus(200)
            ->withData($data);

        $this->response->setHtml($payload);
    }
}


