<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Entries\Page;
use Daylog\Presentation\Controllers\BaseController;

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
        echo 'Create entry';
    }
}
