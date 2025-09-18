<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries\Page;
use Daylog\Presentation\Controllers\BaseController;
use Daylog\Presentation\Views\ResponsePayload;

/**
 * Controller for displaying the entry creation form (HTML).
 */
final class EntryCreatePageController extends BaseController
{
    /**
     * Show the entry creation page.
     *
     * @return void
     */
    public function show(): void
    {
        $data = [
            'template' => 'create.html',
            'script'   => 'create.js',
        ];

        $payload = ResponsePayload::success()
            ->withStatus(200)
            ->withData($data);

        $this->response->setHtml($payload);
    }
}
