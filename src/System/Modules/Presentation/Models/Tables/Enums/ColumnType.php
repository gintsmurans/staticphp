<?php

namespace System\Modules\Presentation\Models\Tables\Enums;

class ColumnType
{
    const TEXT = 'text';
    const DATE = 'date';
    const DATETIME = 'datetime';
    const DATEINTERVAL = 'dateinterval';
    const SELECT = 'select';
    const SELECT_ALL_CHECKBOX = 'select_all_checkboxes'; // Special case
}
