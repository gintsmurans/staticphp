<?php

namespace System\Modules\Presentation\Models\Tables\Traits;

use System\Modules\Presentation\Models\Tables\Interfaces\TableInterface;

trait TableInstance
{
    protected TableInterface $tableInstance;

    public function __construct(TableInterface &$tableInstance)
    {
        $this->tableInstance = &$tableInstance;
    }
}
