<?php
declare(strict_types=1);

namespace Daylog\Presentation\Views\Renderers;

/**
 * Contract for all view renderers (HTML, JSON, etc.).
 *
 * Purpose:
 * Provide a minimal, stable API for the Presentation layer to render
 * a final HTTP body as a string. Concrete implementations are responsible
 * for applying headers (e.g., Content-Type, status) and formatting.
 *
 * Mechanics:
 * - Controllers (via ViewResponse) pass a normalized data array.
 * - Implementations (e.g., HtmlView, JsonView) set headers internally
 *   and return the rendered body.
 */
interface ViewRendererInterface
{
    /**
     * Render the response into a final string.
     *
     * The implementation SHOULD set all necessary HTTP headers
     * (status code, Content-Type) before producing the body.
     * Data is expected to be a normalized, presentation-ready array.
     *
     * @param array<string,mixed> $data Normalized payload prepared by the controller/ViewResponse.
     *
     * @return string Rendered HTTP body (ready to echo).
     */
    public function render(array $data): string;
}
