<?php
declare(strict_types=1);

namespace Daylog\Presentation\Controllers;

abstract class BaseController
{
    protected $response;

    public function __construct()
    {
        $this->response = null;
    }

    /**
     * Kick start the View, which creates the response
     * based on our previously set content data.
     * finally echo the response or overwrite this method
     * and do something else with it.
     * 
     */
    public function afterroute(): void
    {
        if (null == $this->response) {
            die('No View has been set.');
        }

        $response = $this->response->render();

        echo $response;
    }
}