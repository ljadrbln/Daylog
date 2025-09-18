<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries\Page;
use Daylog\Presentation\Controllers\BaseController;

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
        echo 'Entries list';
    }
}
