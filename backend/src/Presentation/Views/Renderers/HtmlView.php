<?php
declare(strict_types=1);

namespace Daylog\Presentation\Views\Renderers;

use Base;
use Template;

/**
 * HtmlView handles rendering HTML templates.
 */
final class HtmlView extends BaseView
{
    protected function getDefaultContentType(): string
    {
        return 'text/html; charset=UTF-8';
    }

    /**
     * Render response data into an HTML template.
     *
     * @param array<string,mixed> $payload
     * @return string
     */
    public function render(array $payload): string
    {
        $this->setHeaders($payload);

        $data = $payload['data'];
        
        $template = $data['template'];
        $script   = $data['script'];

        //Base::instance()->mset([]);
        //$html = Template::instance()->render($template);

        $html = sprintf('%s - %s', $template, $script);
        return $html;
    }
}
