<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries\Page;
use Daylog\Presentation\Controllers\BaseController;

/**
 * Controller for displaying the entry editing form (HTML).
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
        echo 'Edit entry';
    }
}
