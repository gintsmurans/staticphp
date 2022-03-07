<?php

namespace System\Modules\Presentation\Models\Tables\Interfaces;

/**
 * Table Interface
 */
interface TableInstanceInterface
{
    public function __construct(TableInterface &$tableInstance);
}
