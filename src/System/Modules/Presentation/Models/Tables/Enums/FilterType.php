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
    case DATE_NATIVE = 'date_native';
    case DATETIME_NATIVE = 'datetime_native';
    case DATEINTERVAL_NATIVE = 'dateinterval_native';
}
