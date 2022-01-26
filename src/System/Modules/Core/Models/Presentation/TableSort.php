<?php

namespace Core\Models\Presentation;

/**
 * Html table sort model.
 *
 * Handles table sorting.
 */
class TableSort
{
    /**
     * String used to parse sort
     *
     * (default value: '')
     *
     * @var string
     * @access protected
     */
    protected $sort_str = '';

    /**
     * Global url prefix
     *
     * (default value: '/')
     *
     * @var string
     * @access protected
     */
    protected $sort_url_prefix = '/';

    /**
     * Sort column map.
     *
     *  Example:
     *  [
     *      '_id' => 'department',
     *
     *      'department' => ['title' => 'Department', 'sort_by' => 'department', 'direction' => 'asc'],
     *      'vcost'      => ['title' => 'Variable costs €', 'sort_by' => 'sum_variable'],
     *      'scost'      => ['title' => 'Static costs €', 'sort_by' => 'sum_static'],
     *      'tcost'      => ['title' => 'Total €', 'sort_by' => 'total'],
     *  ]
     *
     * (default value: [])
     *
     * @var array
     * @access protected
     */
    protected $sort_column_map = [];


    /**
     * Construct tableSort
     *
     * @access public
     * @param  array    $sort_columns
     * @param  string   $url_prefix (default: [empty string])
     * @return void
     */
    public function __construct($sort_columns, $url_prefix = '')
    {
        $this->sort_column_map = $sort_columns;
        $this->sort_url_prefix = $url_prefix;
    }


    /**
     * Set and retrieve url
     *
     * @access public
     * @param  string|null  $set_url (default: null)
     * @return string|void
     */
    public function url($set_url = null)
    {
        if ($set_url === null) {
            return $this->sort_url_prefix;
        }

        $this->sort_url_prefix = $set_url;
    }


    /**
     * Parse and find sort by and direction values in sort query string.
     *
     * $sort_str format: "[field]=[direction],[field]=[direction],..", e.g. name=asc,created=desc
     *
     * @access public
     * @param  string      $sort_str
     * @param  string|null $set_prefix (default: null)
     * @return void
     */
    public function parse($sort_str, $set_prefix = null)
    {
        if (!empty($sort_str)) {
            $this->sort_str = $sort_str;
            $sort = parseQueryString($sort_str, ';');
            foreach ($sort as $key => $value) {
                if (isset($this->sort_column_map[$key])) {
                    $this->sort_column_map['_id'] = $key;
                    $column = & $this->sort_column_map[$key];
                    $column['direction'] = ($value == 'desc' ? 'desc' : 'asc');
                    break;
                }
            }
        }

        if ($set_prefix !== null) {
            $this->sort_column_map['_prefix'] = $set_prefix;
        }
    }


    /**
     * Get html link or url for specified table and column
     *
     * @access public
     * @param  string $for_column
     * @param  bool $url_only (default: false)
     * @return string|bool Returns string of html link
     */
    public function sortLink($for_column, $url_only = false)
    {
        if (isset($this->sort_column_map[$for_column]) === false) {
            throw new \Exception("Missing sort column: {$for_column}");
        }

        $needle_column = $this->sort_column_map[$for_column];
        if (is_array($needle_column) == false || !isset($needle_column['title'])) {
            return false;
        }

        if (!empty($needle_column['no_sort'])) {
            return $needle_column['title'];
        }

        $sort_column_id = $this->sort_column_map['_id'];
        $sort_column    = $this->sort_column_map[$sort_column_id];
        $url            = '';

        if (!empty($this->sort_column_map['_prefix'])) {
            $url .= $this->sort_column_map['_prefix'];
        }
        $url .= $for_column.'=';
        $url .= ($sort_column_id === $for_column && $sort_column['direction'] == 'asc' ? 'desc' : 'asc');

        if (strpos($this->sort_url_prefix, '%sort') !== false) {
            $url = str_replace('%sort', $url, $this->sort_url_prefix);
        } else {
            $url = $this->sort_url_prefix.$url;
        }

        // Return only the url
        if ($url_only === true) {
            return $url;
        }

        // Generate html link
        $html = '';
        if ($sort_column_id === $for_column) {
            $html = '&nbsp;&nbsp;<span class="fa fa-chevron-';
            $html .= ($sort_column['direction'] == 'asc' ? 'down' : 'up');
            $html .= ' font-size-11"></span>';
        }

        $link_addon = (empty($needle_column['sort_link_attr']) ? '' : $needle_column['sort_link_attr']);
        if (!empty($needle_column['description'])) {
            $link_addon = ' title="'.$needle_column['description'].'" class="tooltip-line" data-toggle="tooltip" data-placement="top"';
        }
        $link = '<div class="hidden-print d-print-none"><a href="'.$url.'" '.$link_addon.'>'.$needle_column['title'].'</a></div><div class="visible-print d-none d-print-inline">'.$needle_column['title'].'</div>'.$html;
        $link = '<div class="d-flex align-items-center">'.$link.'</div>';
        return $link;
    }


    /**
     * Returns table's header row for all columns
     *
     * @access public
     * @return string   Returns string containing table row
     */
    public function tableHeaderRow()
    {
        $html = '<tr>';

        foreach ($this->sort_column_map as $key => $column) {
            $show_column = true;
            if (isset($column['show_column'])) {
                if (is_callable($column['show_column'])) {
                    $show_column = $column['show_column']();
                } else {
                    $show_column = (bool)$column['show_column'];
                }
            }

            $test = $this->sortLink($key);
            if ($test !== false && $show_column !== false) {
                $attributes = (!empty($column['col_attr']) ? ' '.$column['col_attr'] : '');
                $html .= '<th'.$attributes.'>'.$test.'</th>';
            }
        }

        $html .= '</tr>';
        return $html;
    }


    /**
     * Returns sort as string that was used to parse sorting
     *
     * @access public
     * @return string
     */
    public function sortString()
    {
        return $this->sort_str;
    }


    /**
     * Get column to sort by against the database
     *
     * @access public
     * @return string   Returns string of sort instructions for database
     */
    public function sortBy()
    {
        $sort_id = $this->sort_column_map['_id'];
        $sort_by = $this->sort_column_map[$sort_id]['sort_by'];
        if (is_callable($sort_by)) {
            return $sort_by($this->sort_column_map[$sort_id]['direction']);
        }

        return $this->sort_column_map[$sort_id]['sort_by'];
    }


    /**
     * Get database compatible sort direction
     *
     * @access public
     * @return string      Returns string of direction to sort (asc|desc)
     */
    public function sortDirection()
    {
        $sort_id = $this->sort_column_map['_id'];
        $sort_by = $this->sort_column_map[$sort_id]['sort_by'];
        if (is_callable($sort_by)) {
            return ''; // Custom sort function
        }
        return $this->sort_column_map[$sort_id]['direction'];
    }
}
