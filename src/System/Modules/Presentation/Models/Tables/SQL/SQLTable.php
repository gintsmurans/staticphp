<?php

namespace System\Modules\Presentation\Models\Tables\SQL;

use System\Modules\Presentation\Models\Tables\Table;

class SQLTable extends Table
{
    public ?SQLSort $sqlSort = null;
    public ?SQLFilters $sqlFilter = null;
    public ?SQLPagination $sqlPagination = null;

    public function initData(?string $filterData = null, ?string $sortData = null, ?int $page = null): void
    {
        parent::initData($filterData, $sortData, $page);

        $this->sqlSort = new SQLSort($this);
        $this->sqlFilter = new SQLFilters($this);
        $this->sqlPagination = new SQLPagination($this);
    }
}
