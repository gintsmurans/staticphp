<?php

namespace System\Modules\Presentation\Models\Tables;

class Table
{
    protected ?array $columns = null;
    protected ?array $rows = null;

    public function __construct($columns, $rows = null)
    {
        $this->columns = $columns;
        if (!empty($rows)) {
            $this->parse($rows);
        }
    }

    public function parse($rows)
    {
        $this->rows = $rows;
    }
}
