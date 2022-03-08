<?php

namespace System\Modules\Presentation\Models\Tables\Enums;

enum RowPosition: string
{
    case HEAD_TOP = 'head_top';
    case HEAD_BOTTOM = 'head_bottom';

    case BODY_TOP = 'body_top';
    case BODY_BOTTOM = 'body_bottom';

    case FOOT_TOP = 'foot_top';
    case FOOT_BOTTOM = 'foot_bottom';
}
