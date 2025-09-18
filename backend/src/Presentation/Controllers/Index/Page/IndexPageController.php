<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers\Index\Page;
use Daylog\Presentation\Controllers\BaseController;

/**
 * Controller for displaying the index page (HTML).
 */
final class IndexPageController extends BaseController
{
    /**
     * Show the entries list page.
     *
     * @return void
     */
    public function show(): void
    {
        \Base::instance()->reroute('/entries');
    }
}
