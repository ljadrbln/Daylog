<?php
declare(strict_types=1);

namespace Daylog\Presentation\Views\Renderers;

use Daylog\Presentation\Views\Renderers\BaseView;

/**
 * JsonView handles rendering of JSON responses.
 */
final class JsonView extends BaseView
{
    /**
     * Return default Content-Type for JSON.
     *
     * @return string
     */
    protected function getDefaultContentType(): string
    {
        return 'application/json; charset=UTF-8';
    }

    /**
     * Render response data as JSON string.
     *
     * @param array<string,mixed> $data
     * @return string
     */
    public function render(array $data): string
    {
        $this->setHeaders($data);
        $data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        return $data;
    }
}
