<?php

namespace System\Modules\Core\Exceptions\ErrorMessage;

use System\Modules\Core\Exceptions\ErrorMessage;

class Forbidden extends ErrorMessage
{
    public function __construct(
        ?string $description = null,
        string $outputType = ErrorMessage::OUTPUT_TYPE_HTML,
        bool $includeHtmlTemplate = true
    ) {
        parent::__construct(
            'Forbidden',
            403,
            "No access to the resource. {$description}",
            $outputType,
            403,
            $includeHtmlTemplate
        );
    }
}
