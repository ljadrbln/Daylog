<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers;
use Daylog\Presentation\Views\ViewResponse;

abstract class BaseController
{
    protected ViewResponse $response;

    public function __construct()
    {
        $this->response = new ViewResponse();
    }

    /**
     * Kick start the View, which creates the response based on our previously set content data.
     * Finally echo the response or overwrite this method and do something else with it.
     */
    public function afterroute(): void
    {
        if (!$this->response->isReady()) {
            $message = 'No view has been set for emission.';
            throw new RuntimeException($message);
        }

        $output = $this->response->render();
        echo $output;
    }
}