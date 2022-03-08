<?php

namespace System\Modules\Presentation\Models\Tables\Enums;

enum ColumnType: string
{
    case TEXT = 'text';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case DATEINTERVAL = 'dateinterval';
    case SELECT = 'select';
    case SELECT_MULTIPLE = 'select-multiple';
    case SELECT_ALL_CHECKBOX = 'select_all_checkboxes'; // Special case
}
