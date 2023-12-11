<?php

namespace System\Modules\Core\Exceptions\ErrorMessage;

use System\Modules\Core\Exceptions\ErrorMessage;

class NotFound extends ErrorMessage
{
    public function __construct()
    {
        parent::__construct(
            httpStatusCode: 404
        );
    }
}
