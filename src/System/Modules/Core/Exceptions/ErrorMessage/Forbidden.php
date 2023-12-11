<?php

namespace System\Modules\Core\Exceptions\ErrorMessage;

use System\Modules\Core\Exceptions\ErrorMessage;

class Forbidden extends ErrorMessage
{
    public function __construct()
    {
        parent::__construct(
            httpStatusCode: 403
        );
    }
}
