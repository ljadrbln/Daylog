<?php
declare(strict_types=1);

namespace Daylog\Presentation\Views\Renderers;
use Daylog\Presentation\Views\Renderers\ViewRendererInterface;

/**
 * BaseView provides common logic for all renderers (JSON, HTML, etc.).
 *
 * Purpose:
 * - Define contract for renderers: each must expose render(array $data): string.
 * - Provide helper for setting headers and status codes.
 */
abstract class BaseView implements ViewRendererInterface
{
    /**
     * Render the response into a string.
     *
     * Must be implemented by concrete renderers (JsonView, HtmlView).
     *
     * @param array<string,mixed> $data
     * @return string
     */
    abstract public function render(array $data): string;

    /**
     * Set HTTP headers before rendering.
     *
     * Uses 'status' from $data (defaults to 200) and content type from getDefaultContentType().
     *
     * @param array<string,mixed> $data
     * @return void
     */
    protected function setHeaders(array $data): void
    {
        $status = $data['status'] ?? 200;
        http_response_code($status);

        $contentType = $this->getDefaultContentType();
        header("Content-Type: {$contentType}");
    }

    /**
     * Return default Content-Type for this renderer.
     *
     * @return string
     */
    abstract protected function getDefaultContentType(): string;
}
