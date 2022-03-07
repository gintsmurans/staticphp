<?php

namespace System\Modules\Presentation\Models\Tables;

use System\Modules\Presentation\Models\Tables\Interfaces\ColumnInterface;
use System\Modules\Presentation\Models\Tables\Enums\ColumnType;
use System\Modules\Presentation\Models\Tables\Enums\FilterType;
use System\Modules\Presentation\Models\Tables\Enums\SortDirection;
use System\Modules\Presentation\Models\Tables\Enums\SortNulls;

class Column implements ColumnInterface
{
    // Default
    public string $id;
    public ColumnType $type = ColumnType::TEXT;
    public string $title = '';
    public string $description = '';

    // Column
    public bool|\Closure $showColumn = true;
    public string $columnAttributes = '';

    // Sort
    public bool $sortEnabled = true;
    public null|string|\Closure $sortBy = null;
    public bool $sortDefaultColumn = false;
    public SortDirection $sortDefaultDirection = SortDirection::ASC;
    public SortNulls $sortNulls = SortNulls::FIRST;
    public ?string $sortLinkAttribute = null;

    // Filter
    public bool $filterHidden = false;
    public bool $filterEnabled = true;
    public ?string $filterTitle = null;
    public ?string $filterDefaultValue = null;
    public ?string $filterDateValue = null;

    public ?string $filterInputAttributes = null;

    public ?array $filterSelectOptions = null;
    public ?string $filterSelectOptionsIdKey = null;
    public ?string $filterSelectOptionsTitleKey = null;
    public ?array $filterSelectOptionsGroups = null;
    public ?string $filterSelectOptionsGroupTitleKey = null;
    public bool $filterSelectMultiple = false;
    public bool $filterSelectSkipEmptyDefault = false;
    public bool $filterSelectDefaultDisabled = false;

    public FilterType $filterType = FilterType::TEXT;
    public null|string|\Closure $filterBy = null;
    public null|array|\Closure $filterData = null;
    public bool $filterSqlDate = false;

    // Data
    public null|string|\Closure $dataKey;

    // Export
    public null|string|\Closure $exportKey;


    public function __construct($id, $settings)
    {
        $this->id = $id;
        foreach ($settings as $key => $value) {
            if (property_exists($this, $key) == false) {
                throw new \Exception("\"{$key}\" does not exists on Column");
            }

            $this->{$key} = $value;
        }
    }
}
