<?php

namespace System\Modules\Presentation\Models\Tables\Output;

use System\Modules\Presentation\Models\Tables\Interfaces\OutputInterface;

use System\Modules\Presentation\Models\Tables\Enums\TableType;
use System\Modules\Presentation\Models\Tables\Enums\RowPosition;
use System\Modules\Presentation\Models\Tables\Enums\SortDirection;

use System\Modules\Presentation\Models\Tables\Traits\TableInstance;

use System\Modules\Presentation\Models\Tables\Column;
use System\Modules\Presentation\Models\Tables\Enums\ColumnType;

class Html implements OutputInterface
{
    use TableInstance;

    public TableType $type = TableType::FULL_HTML;
    public string $classNames = 'table';


    // ! Sort

    /**
     * Get html link or url for specified table and column
     *
     * @access public
     * @param  Column $forColumn
     * @param  bool $urlOnly (default: false)
     * @return string Returns link for a column
     */
    public function sortLink(Column $forColumn): string
    {
        $newDirection = (
            $forColumn->id === $this->tableInstance->sort->currentColumn()->id
            && $this->tableInstance->sort->currentDirection() === SortDirection::ASC
            ? 'desc' : 'asc'
        );
        $url = "{$forColumn->id}={$newDirection}";

        if (strpos($this->tableInstance->sort->url(), '%sort') !== false) {
            $url = str_replace('%sort', $url, $this->tableInstance->sort->url());
        } else {
            $url = $this->tableInstance->sort->url()."/{$url}";
        }

        return $url;
    }

    /**
     * Get html link for a column
     *
     * @access public
     * @param  Column $forColumn
     * @return string Returns string containing html link
     */
    public function sortLinkHtml(Column $forColumn): string
    {
        if ($forColumn->sortEnabled === false) {
            return $forColumn->title;
        }

        $url = $this->sortLink($forColumn);

        $html = '';
        if ($forColumn->id === $this->tableInstance->sort->currentColumn()->id) {
            $html = '&nbsp;&nbsp;<span class="fa fa-chevron-';
            $html .= ($this->tableInstance->sort->currentDirection() === SortDirection::ASC ? 'down' : 'up');
            $html .= ' font-size-11"></span>';
        }

        $link_addon = (empty($forColumn->sortLinkAttribute) ? '' : $forColumn->sortLinkAttribute);
        if (!empty($forColumn->description)) {
            $link_addon = ' title="'.$forColumn->description.'" class="tooltip-line" data-toggle="tooltip" data-placement="top"';
        }
        $link = '<div class="hidden-print d-print-none"><a href="'.$url.'" '.$link_addon.'>'.$forColumn->title.'</a></div>';
        $link .= '<div class="visible-print d-none d-print-inline">'.$forColumn->title.'</div>'.$html;

        $link = '<div class="d-flex align-items-center">'.$link.'</div>';
        return $link;
    }

    /**
     * Returns table's header row for all columns
     *
     * @access public
     * @return string   Returns string containing table row
     */
    public function titleRow(): string
    {
        $html = '<tr>';

        /** @var Column $column */
        $column = null;
        foreach ($this->tableInstance->columns as $column) {
            $showColumn = true;
            if (is_callable($column->showColumn)) {
                $showColumn = $column->showColumn();
            } else {
                $showColumn = $column->showColumn;
            }

            $linkHtml = $this->sortLinkHtml($column);
            if ($showColumn !== false) {
                $attributes = (!empty($column->columnAttributes) ? ' '.$column->columnAttributes : '');
                $html .= '<th'.$attributes.'>'.$linkHtml.'</th>';
            }
        }
        $html .= '</tr>';

        return $html;
    }


    // ! Filters
    /**
     * Returns html input attribute - "value" with its value
     *
     * @access public
     * @param  string       $field
     * @param  string|null  $compare (default: null)
     * @return string
     */
    public function inputValue(string $field, ?string $compare = null): string
    {
        $parsedData = $this->tableInstance->filter->parsedData();

        if ($compare !== null) {
            if (isset($parsedData[$field]['value'])) {
                if (is_array($parsedData[$field]['value'])) {
                    if (in_array($compare, $parsedData[$field]['value'])) {
                        return ' selected="selected"';
                    }
                } elseif ($parsedData[$field]['value'] === $compare) {
                    return ' selected="selected"';
                }
            }

            return '';
        }

        $attributes = '';
        if (isset($parsedData[$field])) {
            $attributes .= ' value="'.str_replace('"', '&quot;', $parsedData[$field]['title']).'"';
            if ($parsedData[$field]['title'] != $parsedData[$field]['value']) {
                $attributes .= ' data-value="'.str_replace('"', '&quot;', $parsedData[$field]['value']).'"';
            }
        }

        return $attributes;
    }

    /**
     * Returns filter input field by $type with value filled in, or selected in case of select html element.
     *
     * @access public
     * @param  string   $name
     * @param  mixed    $value (default: [empty string])
     * @return string|bool
     */
    public function inputField(Column $forColumn, string $value = ''): string
    {
        if ($forColumn->filterHidden === true) {
            return '';
        }

        $classes = 'form-control form-control-sm input-xs filter';
        $attributes = ' id="filter_'.$forColumn->id.'" ';
        if (!empty($forColumn->filterInputAttributes)) {
            $attributes .= (' '.$forColumn->filterInputAttributes);
        }
        if ($forColumn->filterEnabled === false) {
            $attributes .= ' disabled="disabled"';
        }
        $html = '';
        switch ($forColumn->type) {
            case ColumnType::DATE:
            case ColumnType::DATETIME:
            case ColumnType::DATEINTERVAL:
                if ($forColumn->type == 'date') {
                    $classes .= ' datepicker';
                } elseif ($forColumn->type == 'datetime') {
                    $classes .= ' datetimepicker';
                } elseif ($forColumn->type == 'dateinterval') {
                    $classes .= ' dateintervalpicker';
                }

                if (!empty($forColumn->filterDateValue)) {
                    $attributes .= ' data-unix="'.$forColumn->filterDateValue.'"';
                }

                // no break

            case ColumnType::TEXT:
                $html = '<input type="text" class="'.$classes.'"'.$attributes;
                if (!empty($forColumn->filterTitle)) {
                    $html .= ' placeholder="'.$forColumn->filterTitle.'" ';
                }
                $html .= ' '.$this->inputValue($forColumn->id).'>';
            break;

            case ColumnType::SELECT:
            case ColumnType::SELECT_MULTIPLE:
                if (isset($forColumn->filterSelectOptions) == false || is_array($forColumn->filterSelectOptions) === false) {
                    throw new \Exception("Value for {$forColumn->id} should be [key => value] array");
                }
                if ($forColumn->type == 'select-multiple') {
                    $attributes .= ' multiple="multiple" size="3" ';
                }

                $parsedData = $this->tableInstance->filter->parsedData();
                $classes .= ' form-select form-select-sm';
                $html = '<select class="'.$classes.'"'.$attributes.'>';
                if (count($forColumn->filterSelectOptions) == 0 && isset($parsedData[$forColumn->id])) {
                    $html .= '<option value="'.$parsedData[$forColumn->id]['value'].'">'.$parsedData[$forColumn->id]['title'].'</option>';
                } elseif (empty($forColumn->filterSelectSkipEmptyDefault)) {
                    $html .= '<option value=""'.(!empty($forColumn->filterSelectDefaultDisabled) ? ' disabled="disabled"' : '').'>'.($forColumn->filterTitle ?? '').'</option>';
                }
                if (!empty($forColumn->filterSelectOptionsGroups) && is_array($forColumn->filterSelectOptionsGroups)) {
                    foreach ($forColumn->filterSelectOptionsGroups as $gkey => $gitem) {
                        $final_optgroup_title = (empty($forColumn->filterSelectOptionsGroupTitleKey) ? $gitem : $gitem[$forColumn->filterSelectOptionsGroupTitleKey]);
                        $html .= '<optgroup label="'. $final_optgroup_title .'">';

                        if (isset($forColumn->filterSelectOptions[$gkey])) {
                            foreach ($forColumn->filterSelectOptions[$gkey] as $key => $item) {
                                $final_id = (empty($forColumn->filterSelectOptionsIdKey) ? $key : $item[$forColumn->filterSelectOptionsIdKey]);
                                $final_title = (empty($forColumn->filterSelectOptionsTitleKey) ? $item : $item[$forColumn->filterSelectOptionsTitleKey]);
                                $html .= '<option value="'.$final_id.'"'. $this->inputValue($forColumn->id, $final_id) .'>'.$final_title.'</option>';
                            }
                        }

                        $html .= '</optgroup>';
                    }
                } else {
                    foreach ($forColumn->filterSelectOptions as $key => $item) {
                        $final_id = (empty($forColumn->filterSelectOptionsIdKey) ? $key : $item[$forColumn->filterSelectOptionsIdKey]);
                        $final_title = (empty($forColumn->filterSelectOptionsTitleKey) ? $item : $item[$forColumn->filterSelectOptionsTitleKey]);
                        $html .= '<option value="'.$final_id.'"'. $this->inputValue($forColumn->id, $final_id) .'>'.$final_title.'</option>';
                    }
                }
                $html .= '</select>';
            break;

            case ColumnType::SELECT_ALL_CHECKBOX:
                $id = 'parent_checkbox_'.$this->tableInstance->tableId();
                $html = '<input type="checkbox" class="faCheckbox parent_checkbox" id="'.$id.'"><label for="'.$id.'"></label>';
            break;
        }

        return $html;
    }

    /**
     * Returns table's filter row for all columns
     *
     * @access public
     * @return string   Returns string containing table row
     */
    public function filtersRow(): string
    {
        $html = '<tr id="table_filters_'.$this->tableInstance->tableId().'">';

        /** @var Column $column */
        $column = null;
        foreach ($this->tableInstance->columns as $column) {
            $showColumn = true;
            if (is_callable($column->showColumn)) {
                $showColumn = $column->showColumn();
            } else {
                $showColumn = $column->showColumn;
            }

            $fieldHtml = $this->inputField($column);
            if ($showColumn !== false) {
                $attributes = (!empty($column->columnAttributes) ? ' '.$column->columnAttributes : '');
                $html .= '<td'.$attributes.'>'.$fieldHtml.'</td>';
            }
        }

        $html .= '</tr>';
        return $html;
    }


    // ! BODY
    public function htmlRow(int $index, array $rowItem, string $title = ''): string
    {
        $html = '';
        if (is_callable($rowItem)) {
            $html .= $rowItem($index, $title);
        } else {
            $html = '<tr title="'.$title.'">';
            foreach ($this->tableInstance->columns as $column) {
                $html .= '<td>';
                if (is_callable($column->dataKey)) {
                    $dataKey = $column->dataKey;
                    $html .= $dataKey($column, $index, $rowItem);
                } elseif ($column->dataKey != null && isset($rowItem[$column->dataKey])) {
                    $html .= $rowItem[$column->dataKey];
                }
                $html .= '</td>';
            }
            $html .= '</tr>';
        }

        return $html;
    }

    public function tableBody(): string
    {
        if (empty($this->tableInstance->rows)) {
            $columnCount = count($this->tableInstance->columns);
            return <<<EOL
            <tr><td colspan="{$columnCount}" class="table-empty table-secondary">No record was found</td></td>
            EOL;
        }

        $html = '';
        foreach ($this->tableInstance->rows as $index => $rowItem) {
            $html .= $this->htmlRow($index, $rowItem);
        }

        return $html;
    }

    public function rowWithPosition(RowPosition $position, string $title = ''): string
    {
        $html = '';
        if (!empty($this->tableInstance->avgRow) && $this->tableInstance->avgRowPosition === $position) {
            $html .= $this->htmlRow(-1, $this->tableInstance->avgRow, 'AVG');
        }
        if (!empty($this->tableInstance->sumRow) && $this->tableInstance->sumRowPosition === $position) {
            $html .= $this->htmlRow(-2, $this->tableInstance->sumRow, 'SUM');
        }
        if (!empty($this->tableInstance->customRow) && $this->tableInstance->customRowPosition === $position) {
            $html .= $this->htmlRow(-3, $this->tableInstance->customRow);
        }

        return $html;
    }

    public function paginationUrl(string $url, int $page): string
    {
        return str_replace('%pagination', $page, $url);
    }

    public function paginationLinks(): string
    {
        $pagination = &$this->tableInstance->pagination;
        if ($pagination->pageCount <= 1) {
            return '';
        }

        $url = $pagination->url();
        $pages = '<ul class="pagination">';
        $pages .= '<li class="page-item'.($pagination->currentPage == 1 ? ' disabled' : '').'"><a class="page-link" href="'.$this->paginationUrl($url, 1).'"><span aria-hidden="true">1</span><span class="sr-only">Previous</span></a></li>';
        $pages .= '<li class="page-item'.($pagination->currentPage == 1 ? ' disabled' : '').'"><a class="page-link" href="'.$this->paginationUrl($url, $pagination->prevPage).'"><span aria-hidden="true">&laquo;</span><span class="sr-only">Previous</span></a></li>';

        for ($i = $pagination->pagesFrom; $i <= $pagination->pagesTo; ++$i) {
            if ($i === $pagination->currentPage) {
                $pages .= '<li class="page-item active"><a class="page-link" href="'.$this->paginationUrl($url, $i).'">'.$i.' <span class="sr-only">(current)</span></a></li>';
            } else {
                $pages .= '<li class="page-item"><a class="page-link" href="'.$this->paginationUrl($url, $i).'">'.$i.'</a></li>';
            }
        }

        $pages .= '<li class="page-item'.($pagination->currentPage == $pagination->pageCount ? ' disabled' : '').'"><a class="page-link" href="'.$this->paginationUrl($url, $pagination->nextPage).'"><span aria-hidden="true">&raquo;</span><span class="sr-only">Next</span></a></li>';
        $pages .= '<li class="page-item'.($pagination->currentPage == $pagination->pageCount ? ' disabled' : '').'"><a class="page-link" href="'.$this->paginationUrl($url, $pagination->pageCount).'"><span aria-hidden="true">'.$pagination->pageCount.'</span><span class="sr-only">Last</span></a></li>';
        $pages .= '</ul>';

        return $pages;
    }

    // ! OUTPUT
    public function makeOutput(): string
    {
        $classNames = !empty($this->classNames) ? "{$this->classNames}" : '';
        $html = '';
        if ($this->type === TableType::FULL_HTML) {
            $html = <<<EOL
<div class="card">
    <div class="table-responsive">
EOL;
        }

        $html .= <<<EOL
        <table class="{$classNames}" id="table_{$this->tableInstance->tableId()}">
            <thead>
                {$this->rowWithPosition(RowPosition::HEAD_TOP)}
                {$this->titleRow()}
                {$this->filtersRow()}
                {$this->rowWithPosition(RowPosition::HEAD_BOTTOM)}
            </thead>
            <tbody>
                {$this->rowWithPosition(RowPosition::BODY_TOP)}
                {$this->tableBody()}
                {$this->rowWithPosition(RowPosition::BODY_BOTTOM)}
            </tbody>
            <tfoot>
                {$this->rowWithPosition(RowPosition::FOOT_TOP)}
                {$this->rowWithPosition(RowPosition::FOOT_BOTTOM)}
            </tfoot>
        </table>
EOL;

        if ($this->type === TableType::FULL_HTML) {
            $html .= <<<EOL
    </div>
    <div class="card-footer">
        {$this->paginationLinks()}
    </div>
</div>
EOL;
        }

        return $html;
    }

    public function showOutput(): void
    {
        header('Content-Type: text/html; charset=utf-8');

        echo $this->makeOutput();
    }
}
