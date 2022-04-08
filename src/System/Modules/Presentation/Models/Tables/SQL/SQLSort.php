<?php

namespace System\Modules\Presentation\Models\Tables\SQL;

use System\Modules\Presentation\Models\Tables\Interfaces\TableInstanceInterface;
use System\Modules\Presentation\Models\Tables\Enums\SortNulls;
use System\Modules\Presentation\Models\Tables\Traits\TableInstance;

/**
 * SQL Sort implementation
 */
class SQLSort implements TableInstanceInterface
{
    use TableInstance;

    /**
     * Returns what to do with nulls in a SQL order by statement
     */
    public function sortNulls(): SortNulls
    {
        $column = $this->tableInstance->sort->currentColumn();
        return $column->sortNulls ?? SortNulls::FIRST;
    }

    /**
     * Returns SQL formatted ORDER BY
     */
    public function sortQuery(): string
    {
        $column = $this->tableInstance->sort->sortBy();
        $direction = $this->tableInstance->sort->sortDirection()->value;
        $nulls = $this->sortNulls();
        $nulls = $nulls == SortNulls::FIRST ? 'NULLS FIRST' : 'NULLS LAST';
        return " ORDER BY {$column} {$direction} {$nulls} ";
    }
}
