<?php

namespace System\Modules\Presentation\Models\Tables;

use System\Modules\Presentation\Models\Tables\Interfaces\FiltersInterface;
use System\Modules\Presentation\Models\Tables\Interfaces\TableInterface;

/**
 * Html table filter model.
 *
 * Handles table filtering.
 */
class Filters implements FiltersInterface
{
    /**
     * Table instance
     *
     * (default value: '')
     *
     * @var Table
     * @access protected
     */
    protected TableInterface $tableInstance;

    /**
     * Filter url prefix
     *
     * (default value: '')
     *
     * @var string
     * @access protected
     */
    protected string $urlPrefix = '';

    /**
     * Filter string value, for the use in urls
     *
     * (default value: null)
     *
     * @var ?string
     * @access protected
     */
    protected ?string $filterData = null;

    /**
     * Array holding all parsed filter data
     *
     * Example: ['x' => ['title' => 'This is title', 'value' => '1']]
     *
     * (default value: [])
     *
     * @var array
     * @access protected
     */
    protected array $filterDataParsed = [];


    /**
     * Construct tableFilters
     *
     * @access public
     * @param  Table  $tableInstance
     * @param  string $urlPrefix (default: [empty string])
     * @return void
     */
    public function __construct(TableInterface &$tableInstance, string $urlPrefix = '', ?string $filterData = null)
    {
        $this->tableInstance = &$tableInstance;
        $this->urlPrefix = $urlPrefix;

        $this->parse($filterData);
    }

    /**
     * Set and retrieve url
     *
     * @access public
     * @return string
     */
    public function url(): ?string
    {
        if ($this->urlPrefix === null) {
            return null;
        }

        return (
            strpos($this->urlPrefix, '%filter') === false ?
                $this->urlPrefix . '%filter' :
                $this->urlPrefix
        );
    }

    /**
     * Set and retrieve url
     *
     * @access public
     * @param  string $setUrl (default: null)
     * @return void
     */
    public function setUrl(?string $setUrl = null): void
    {
        $this->urlPrefix = $setUrl;
    }

    /**
     * Returns string that was used for filter
     *
     * @access public
     * @return string
     */
    public function filterData(): string
    {
        return $this->filterData;
    }

    /**
     * Returns array of filter data
     *
     * @access public
     * @return array
     */
    public function parsedData(): array
    {
        return $this->filterDataParsed;
    }

    /**
     * Returns array of filter data
     *
     * @access public
     * @return array
     */
    public function setParsedData(string $key, array $value): array
    {
        return $this->filterDataParsed[$key] = $value;
    }

    /**
     * Returns boolean specifying whether there is a specific filter used
     *
     * @access public
     * @return bool
     */
    public function hasFilter(string $key): bool
    {
        return empty($this->filterDataParsed[$key]) === false;
    }

    /**
     * Returns boolean specifying whether there is a specific filter used
     *
     * @access public
     * @return mixed
     */
    public function filterValue(string $key): mixed
    {
        return isset($this->filterDataParsed[$key]) ? $this->filterDataParsed[$key]['value'] : false;
    }

    /**
     * Parse filter
     *
     * @access public
     * @param  ?string  $filterData
     * @param  ?Closure $callback
     * @return void
     */
    public function parse(?string $filterData = null, ?\Closure $formatter = null): void
    {
        $this->filterData = $filterData;

        $filter = [];
        if (!empty($filterData)) {
            $filter = $this->tableInstance->parseQueryString($filterData, ';');
            foreach ($filter as $key => $value) {
                $filter[$key] = ['title' => $value, 'value' => $value];
            }
        }

        // Add default values to the filter
        /** @var Column $column */
        $column = null;
        foreach ($this->tableInstance->columns as $column) {
            if (isset($column->filterDefaultValue) && !isset($filter[$column->id])) {
                if (is_array($column->filterDefaultValue)) {
                    $filter[$column->id] = $column->filterDefaultValue;
                } else {
                    $filter[$column->id] = [
                        'title' => $column->filterDefaultValue,
                        'value' => $column->filterDefaultValue
                    ];
                }
            }
        }

        // Run filters through a formatter
        if (is_callable($formatter)) {
            foreach ($filter as $key => $value) {
                $filter[$key] = $formatter($key, $value);
            }
        }

        $this->filterDataParsed = $filter;
    }

    /**
     * TODO: Not sure if we need this
     * Adds filter to filters. Should be used after parse.
     *
     * @access public
     * @return void
     */
    public function addFilter(string $key, string $query, ?array $params = null, ?array $data = null): void
    {
        $this->filterQuery[] = $query;

        if ($params !== null) {
            if (is_array($params)) {
                $this->filterParams = array_merge($this->filterParams, $params);
            } else {
                $this->filterParams[] = $params;
            }
            $this->filterParamsByKey[$key] = $params;
        }

        if ($data !== null) {
            $this->filterDataParsed[$key] = $data;
        }
    }
}
