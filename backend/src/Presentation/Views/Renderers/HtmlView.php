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
     * @param array<string,mixed> $data
     * @return string
     */
    public function render(array $data): string
    {
        $this->setHeaders($data);

        $template = $data['template'] ?? 'templates/layout.html';
        $vars     = $data['vars'] ?? [];

        Base::instance()->mset($vars);
        $html = Template::instance()->render($template);

        return $html;
    }
}
