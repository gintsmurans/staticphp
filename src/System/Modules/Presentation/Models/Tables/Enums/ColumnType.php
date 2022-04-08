<?php

namespace System\Modules\Presentation\Models\Tables\Enums;

enum ColumnType: string
{
    case DATE = 'date';
    case DATETIME = 'datetime';
    case DATEINTERVAL = 'dateinterval';

    case SELECT = 'select';
    case SELECT_MULTIPLE = 'select-multiple';

    case SWITCH = 'switch';

    case MULTILINE_TEXT = 'multiline-text';
    case TEXT = 'text';

    // Specific cases
    case ROW_NUMBER = 'row-number';
    case SELECT_ALL_CHECKBOX = 'select-all-checkboxes';
}
