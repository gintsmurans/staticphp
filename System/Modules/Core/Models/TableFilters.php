<?php

namespace Core\Models;


/**
 * Html table filter model.
 *
 * Handles table filtering.
 */
class TableFilters
{
    /**
     * Quite unique table id
     *
     * (default value: '')
     *
     * @var string
     * @access protected
     */
    protected $table_id = '';

    /**
     * Filter url prefix
     *
     * (default value: '')
     *
     * @var string
     * @access protected
     */
    protected $filter_url_prefix = '';

    /**
     * Sort column map.
     *
     *  Example:
     *  [
     *      'week'          => ['type' => 'text', 'title' => 'Week'],
     *      'design'        => ['type' => 'text', 'title' => 'Product design'],
     *      'g_per_package' => ['type' => 'text', 'title' => 'G/package'],
     *      'box_design'    => ['type' => 'text', 'title' => 'Box design'],
     *      'ean'           => ['type' => 'text', 'title' => 'EAN'],
     *      'order_number'  => ['type' => 'text', 'title' => 'Order number'],
     *  ]
     *
     * (default value: [])
     *
     * @var array
     * @access protected
     */
    protected $filter_column_map = [];

    /**
     * Filter string value, for the use in urls
     *
     * (default value: '')
     *
     * @var string
     * @access protected
     */
    protected $filter_str = '';

    /**
     * Array of database query strings containing all filters used in search
     *
     * (default value: [])
     *
     * @var array
     * @access protected
     */
    protected $filter_query = [];

    /**
     * Array of all parametrs
     *
     * (default value: [])
     *
     * @var array
     * @access protected
     */
    protected $filter_params = [];

    /**
     * Array of all parametrs by filter key
     *
     * (default value: [])
     *
     * @var array
     * @access protected
     */
    protected $filter_params_by_key = [];

    /**
     * Array holding all parsed filter data
     *
     * (default value: [])
     *
     * @var array
     * @access protected
     */
    protected $filter_data = [];


    /**
     * Construct tableFilters
     *
     * @access public
     * @param  array    $filter_columns
     * @param  string   $url_prefix (default: [empty string])
     * @return void
     */
    public function __construct($filter_columns, $url_prefix = '')
    {
        $this->table_id          = md5(time().mt_rand(1, 100));
        $this->filter_column_map = $filter_columns ?? [];
        $this->filter_url_prefix = $url_prefix;
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
            return (
                strpos($this->filter_url_prefix, '%filter') === false ?
                    $this->filter_url_prefix.'%filter' :
                    $this->filter_url_prefix
            );
        }

        $this->filter_url_prefix = $set_url;
    }


    /**
     * Parse filter
     *
     * @access public
     * @param  string   $filter
     * @param  callable $callback
     * @return void
     */
    public function parse($filter_str, $callback)
    {
        if (is_callable($callback) == false) {
            throw new \Exception('Callback must be executable');
        }

        $filter = [];
        if (!empty($filter_str)) {
            $this->filter_str = $filter_str;
            $filter = parseQueryString($filter_str, ';');
        }

        // Add default values to the filter
        foreach ($this->filter_column_map as $key => $item) {
            if (isset($item['default']) && !isset($filter[$key])) {
                $filter[$key] = $item['default'];
            }
        }

        if (!empty($filter)) {
            foreach ($filter as $key => $value) {
                $data = $callback($key, $value);
                if (isset($data['query'])) {
                    $this->filter_query[] = $data['query'];
                }

                if (isset($data['param'])) {
                    if (is_array($data['param'])) {
                        $this->filter_params = array_merge($this->filter_params, $data['param']);
                    } else {
                        $this->filter_params[] = $data['param'];
                    }
                    $this->filter_params_by_key[$key] = $data['param'];
                }

                if (isset($data['data'])) {
                    $this->filter_data[$key] = $data['data'];
                }
            }
        }
    }


    /**
     * Sets / Returns columns
     *
     * @access public
     * @return array|bool
     */
    public function columns($new_columns = null)
    {
        if ($new_columns === null) {
            return $this->filter_column_map;
        } else {
            $this->filter_column_map = $new_columns;
        }
    }


    /**
     * Returns boolean specifying whether there is any filter to use
     *
     * @access public
     * @return bool
     */
    public function hasQuery()
    {
        return empty($this->filter_query) === false;
    }


    /**
     * Returns $prefix concatenated with filter keys for SQL query
     *
     * @access public
     * @param  string   $prefix
     * @return string
     */
    public function query($prefix = 'WHERE')
    {
        if (empty($this->filter_query) === true) {
            return '';
        }

        return ' '.$prefix.' '.implode(' AND ', $this->filter_query);
    }


    /**
     * Returns filter keys as array
     *
     * @access public
     * @return array
     */
    public function queryArray()
    {
        return $this->filter_query;
    }


    /**
     * Returns array of filter values
     *
     * @access public
     * @return array
     */
    public function params($key = null)
    {
        if (!empty($key)) {
            return isset($this->filter_params_by_key[$key]) ? $this->filter_params_by_key[$key] : false;
        }

        return $this->filter_params;
    }


    /**
     * Returns boolean specifying whether there is a specific filter used
     *
     * @access public
     * @return bool
     */
    public function hasFilter($key)
    {
        return empty($this->filter_data[$key]) === false;
    }


    /**
     * Adds filter to filters. Should be used after parse.
     *
     * @access public
     * @return void
     */
    public function addFilter($key, $query, $params = null, $data = null)
    {
        $this->filter_query[] = $query;

        if ($params !== null) {
            if (is_array($params)) {
                $this->filter_params = array_merge($this->filter_params, $params);
            } else {
                $this->filter_params[] = $params;
            }
            $this->filter_params_by_key[$key] = $params;
        }

        if ($data !== null) {
            $this->filter_data[$key] = $data;
        }
    }


    /**
     * Returns boolean specifying whether there is a specific filter used
     *
     * @access public
     * @return bool
     */
    public function filterValue($key)
    {
        return isset($this->filter_data[$key]) ? $this->filter_data[$key]['value'] : false;
    }


    /**
     * Returns array of filter data
     *
     * @access public
     * @return array
     */
    public function data()
    {
        return $this->filter_data;
    }


    /**
     * Returns string that was used for filter
     *
     * @access public
     * @return string
     */
    public function filterString()
    {
        return $this->filter_str;
    }


    /**
     * Returns html input attribute - "value" with its value
     *
     * @access public
     * @param  string       $field
     * @param  string|null  $compare (default: null)
     * @return string
     */
    public function inputValue($field, $compare = null)
    {
        if ($compare !== null) {
            if (isset($this->filter_data[$field]['value'])) {
                if (is_array($this->filter_data[$field]['value'])) {
                    if (in_array($compare, $this->filter_data[$field]['value'])) {
                        return ' selected="selected"';
                    }
                } elseif ($this->filter_data[$field]['value'] === $compare) {
                    return ' selected="selected"';
                }
            }

            return '';
        }

        $attributes = '';
        if (isset($this->filter_data[$field])) {
            $attributes .= ' value="'.str_replace('"', '&quot;', $this->filter_data[$field]['title']).'"';
            if ($this->filter_data[$field]['title'] != $this->filter_data[$field]['value']) {
                $attributes .= ' data-value="'.str_replace('"', '&quot;', $this->filter_data[$field]['value']).'"';
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
    public function inputField($name, $value = '')
    {
        if (isset($this->filter_column_map[$name]) === false) {
            throw new \Exception("Missing filter column: {$name}");
        }

        $column = $this->filter_column_map[$name];
        if (is_array($column) == false || !isset($column['title'])) {
            return false;
        }

        if (!empty($column['no_filter'])) {
            return '';
        }

        $classes = 'form-control form-control-sm input-xs filter';
        $attributes = ' id="filter_'.$name.'" ';
        if (!empty($column['input_attr'])) {
            $attributes .= (' '.$column['input_attr']);
        }
        $html = '';
        switch($column['type']) {
            case 'date':
            case 'datetime':
            case 'dateinterval':
                if ($column['type'] == 'date') {
                    $classes .= ' datepicker';
                } elseif ($column['type'] == 'datetime') {
                    $classes .= ' datetimepicker';
                } elseif ($column['type'] == 'dateinterval') {
                    $classes .= ' dateintervalpicker';
                }

                if (!empty($column['date_from'])) {
                    $attributes .= ' data-unix="'.$column['date_from'].'"';
                }

                // Reset to text
                $column['type'] = 'text';

            case 'text':
                $html = '<input type="'.$column['type'].'" class="'.$classes.'"'.$attributes;
                if (!empty($column['filter_title'])) {
                    $html .= ' placeholder="'.$column['filter_title'].'" ';
                }
                $html .= ' '.$this->inputValue($name).'>';
            break;

            case 'select':
                if (isset($column['options']) == false || is_array($column['options']) === false) {
                    throw new \Exception("Value for {$name} should be [key => value] array");
                }

                $html = '<select class="'.$classes.'"'.$attributes.'>';
                if (count($column['options']) == 0 && isset($this->filter_data[$name])) {
                    $html .= '<option value="'.$this->filter_data[$name]['value'].'">'.$this->filter_data[$name]['title'].'</option>';
                }
                elseif (empty($column['skip_empty_default'])) {
                    $html .= '<option value=""'.(!empty($column['default_disabled']) ? ' disabled="disabled"' : '').'>'.(!empty($column['filter_title']) ? $column['filter_title'] : '').'</option>';
                }
                if (!empty($column['options_groupped']) && is_array($column['options_groupped'])) {
                    foreach ($column['options_groupped'] as $gkey => $gitem) {
                        $final_optgroup_title = (empty($column['options_groupped_title_key']) ? $gitem : $gitem[$column['options_groupped_title_key']]);
                        $html .= '<optgroup label="'. $final_optgroup_title .'">';

                        if (isset($column['options'][$gkey])) {
                            foreach ($column['options'][$gkey] as $key => $item) {
                                $final_id = (empty($column['options_id_key']) ? $key : $item[$column['options_id_key']]);
                                $final_title = (empty($column['options_title_key']) ? $item : $item[$column['options_title_key']]);
                                $html .= '<option value="'.$final_id.'"'. $this->inputValue($name, $final_id) .'>'.$final_title.'</option>';
                            }
                        }

                        $html .= '</optgroup>';
                    }
                } else {
                    foreach ($column['options'] as $key => $item) {
                        $final_id = (empty($column['options_id_key']) ? $key : $item[$column['options_id_key']]);
                        $final_title = (empty($column['options_title_key']) ? $item : $item[$column['options_title_key']]);
                        $html .= '<option value="'.$final_id.'"'. $this->inputValue($name, $final_id) .'>'.$final_title.'</option>';
                    }
                }
                $html .= '</select>';
            break;

            case 'select_all_checkbox':
                $id = 'parent_checkbox_'.$this->table_id;
                $html = '<input type="checkbox" class="faCheckbox parent_checkbox" id="'.$id.'"><label for="'.$id.'"></label>';
            break;
        }

        return $html;
    }


    /**
     * Returns table's header row for all columns
     *
     * @access public
     * @return string   Returns string containing table row
     */
    public function tableFilterRow()
    {
        $html = '<tr id="table_filters_'.$this->table_id.'">';

        foreach ($this->filter_column_map as $key => $column) {
            $show_column = true;
            if (!empty($column['show_column'])) {
                if (is_callable($column['show_column'])) {
                    $show_column = $column['show_column']();
                } else {
                    $show_column = (bool)$column['show_column'];
                }
            }
            $test = $this->inputField($key);
            if ($test !== false && $show_column !== false) {
                $attributes = (!empty($column['col_attr']) ? ' '.$column['col_attr'] : '');
                $html .= '<td'.$attributes.'>'.$test.'</td>';
            }
        }

        $html .= '</tr>';
        return $html;
    }


    /**
     * Returns table's unique id
     *
     * @access public
     * @return string
     */
    public function tableId()
    {
        return $this->table_id;
    }
}
