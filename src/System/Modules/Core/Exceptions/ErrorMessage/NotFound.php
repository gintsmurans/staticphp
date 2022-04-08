<?php

namespace System\Modules\Core\Exceptions\ErrorMessage;

use System\Modules\Core\Exceptions\ErrorMessage;

class NotFound extends ErrorMessage
{
    public function __construct(
        ?string $description = null,
        string $outputType = ErrorMessage::OUTPUT_TYPE_HTML,
        bool $includeHtmlTemplate = true
    ) {
        parent::__construct(
            'Not Found',
            404,
            "Page not found. {$description}",
            $outputType,
            404,
            $includeHtmlTemplate
        );
    }
}
