<?php
declare(strict_types=1);

namespace Daylog\Presentation\Views;

use Daylog\Presentation\Views\Renderers\ViewRendererInterface;
use Daylog\Presentation\Views\Renderers\HtmlView;
use Daylog\Presentation\Views\Renderers\JsonView;
use Daylog\Presentation\Views\ResponsePayload;

use RuntimeException;

/**
 * ViewResponse accumulates a renderer and a ResponsePayload, then emits a string.
 */
final class ViewResponse
{
    /** @var ViewRendererInterface|null */
    private ?ViewRendererInterface $view = null;

    /** @var ResponsePayload|null */
    private ?ResponsePayload $payload = null;

    /**
     * Accept ResponsePayload for JSON API responses and select JSON renderer.
     *
     * @param ResponsePayload $response
     * @return void
     */
    public function setJson(ResponsePayload $response): void
    {
        $this->payload = $response;
        $this->view    = new JsonView();
    }

    /**
     * Accept ResponsePayload for HTML page rendering and select HTML renderer.
     *
     * @param ResponsePayload $response
     * @return void
     */
    public function setHtml(ResponsePayload $response): void
    {
        $this->payload = $response;
        $this->view    = new HtmlView();
    }

    /**
     * Render final HTTP body using the selected renderer.
     *
     * @return string
     */
    public function render(): string
    {
        $view    = $this->view;
        $payload = $this->payload;

        if ($view === null || $payload === null) {
            $message = 'ViewResponse is not prepared: renderer or payload is missing.';
            throw new \RuntimeException($message);
        }

        $data   = $this->payload->toArray();
        $result = $this->view->render($data);

        return $result;
    }
}
