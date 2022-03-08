<?php

namespace System\Modules\Presentation\Models\Tables\Enums;

enum FilterType: string
{
    case TEXT = 'text';
    case INT8 = 'int';
    case DECIMAL = 'decimal';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case DATEINTERVAL = 'dateinterval';
}
