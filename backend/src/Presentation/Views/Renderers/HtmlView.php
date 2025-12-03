<?php
declare(strict_types=1);

namespace Daylog\Presentation\Views\Renderers;

use Base;
use RuntimeException;
use Template;

/**
 * HtmlView handles rendering HTML templates using F3 Template engine.
 *
 * Responsibilities:
 * - Set appropriate Content-Type header for HTML responses.
 * - Map generic response payload into F3 hive variables (pageTitle, contentTemplate, pageScripts).
 * - Delegate final rendering to a layout template (layouts/base.html).
 *
 * Expected payload shape:
 * - $payload['data']['template']: string      // content template path, e.g. "pages/entries-list.html"
 * - $payload['data']['pageTitle']: string    // HTML <title> and H1 title
 * - $payload['data']['script']?: string      // optional JS bundle path, e.g. "/assets/js/listEntriesVanilla.js"
 */
final class HtmlView extends BaseView
{
    /**
     * Get default Content-Type header value for HTML responses.
     *
     * @return string Content-Type for HTML output.
     */
    protected function getDefaultContentType(): string
    {
        $contentType = 'text/html; charset=UTF-8';

        return $contentType;
    }

    /**
     * Render response data into an HTML template.
     *
     * The method:
     * - applies HTTP headers via BaseView::setHeaders();
     * - extracts template parameters from $payload['data'];
     * - populates F3 hive variables used by layouts/base.html;
     * - renders the base layout and returns the final HTML string.
     *
     * @param array<string,mixed> $payload Normalized response payload array.
     *
     * @return string Rendered HTML.
     *
     * @throws RuntimeException When required keys are missing in payload data.
     */
    public function render(array $payload): string
    {
        $this->setHeaders($payload);

        /** @var array<string,mixed> $data */
        $data = [];

        if (array_key_exists('data', $payload) && is_array($payload['data'])) {
            /** @var array<string,mixed> $rawData */
            $rawData = $payload['data'];
            $data    = $rawData;
        }

        if (!array_key_exists('template', $data) || !is_string($data['template'])) {
            $message = 'HtmlView expects "template" key in payload data.';
            throw new RuntimeException($message);
        }

        $template = sprintf('pages/%s', $data['template']);

        $pageTitle = 'Daylog';
        if (array_key_exists('pageTitle', $data) && is_string($data['pageTitle'])) {
            $pageTitle = $data['pageTitle'];
        }

        $pageScripts = '';
        if (array_key_exists('script', $data) && is_string($data['script'])) {
            $scriptSrc   = $data['script'];
            $pageScripts = sprintf('<script type="module" src="/assets/js/%s"></script>', $scriptSrc);
        }

        $f3 = Base::instance();
        $f3->set('pageTitle', $pageTitle);
        $f3->set('contentTemplate', $template);
        $f3->set('pageScripts', $pageScripts);

        $layoutPath     = 'layouts/base.html';
        $templateEngine = Template::instance();
        $html           = $templateEngine->render($layoutPath);

        return $html;
    }
}
