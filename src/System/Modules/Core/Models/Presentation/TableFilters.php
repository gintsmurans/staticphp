<?php

namespace Core\Models\Presentation;

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
     * @param  callable|null $callback
     * @return void
     */
    public function parse($filter_str, $callback = null)
    {
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
                $data = [];
                $filter_column = null;
                if (isset($this->filter_column_map[$key])) {
                    $filter_column = $this->filter_column_map[$key];
                }
                if (!empty($filter_column['filter_type']) && !empty($filter_column['filter_by'])) {
                    if (is_callable($filter_column['filter_by'])) {
                        $data = $filter_column['filter_by']($value);
                    } else {
                        $filter_match = isset($filter_column['filter_match']) ? $filter_column['filter_match'] : 'default';
                        $data = self::runFilter($filter_column['filter_type'], $filter_column['filter_by'], $value, $filter_match);
                    }
                } elseif ($callback !== null) {
                    $data = $callback($key, $value);
                }

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

                if (isset($filter_column['filter_data'])) {
                    if (is_callable($filter_column['filter_data'])) {
                        $test = $filter_column['filter_data']($value);
                        if ($test !== null) {
                            $this->filter_data[$key] = $test;
                        }
                    } elseif (is_array($filter_column['filter_data'])) {
                        $this->filter_data[$key] = $filter_column['filter_data'];
                    }
                } elseif (isset($data['data'])) {
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
        switch ($column['type']) {
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

                // no break
            case 'text':
                $html = '<input type="'.$column['type'].'" class="'.$classes.'"'.$attributes;
                if (!empty($column['filter_title'])) {
                    $html .= ' placeholder="'.$column['filter_title'].'" ';
                }
                $html .= ' '.$this->inputValue($name).'>';
            break;

            case 'select':
            case 'select-multiple':
                if (isset($column['options']) == false || is_array($column['options']) === false) {
                    throw new \Exception("Value for {$name} should be [key => value] array");
                }
                if ($column['type'] == 'select-multiple') {
                    $attributes .= ' multiple="multiple" size="3" ';
                }

                $classes .= ' form-select form-select-sm';
                $html = '<select class="'.$classes.'"'.$attributes.'>';
                if (count($column['options']) == 0 && isset($this->filter_data[$name])) {
                    $html .= '<option value="'.$this->filter_data[$name]['value'].'">'.$this->filter_data[$name]['title'].'</option>';
                } elseif (empty($column['skip_empty_default'])) {
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
            if (isset($column['show_column'])) {
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


    /**
     * Run local filter funcation based on $filter_type and return query for $filter_by table column.
     *
     * @access public
     * @param  string   $filter_type
     * @param  string   $filter_by
     * @param  mixed    $value
     * @return string[] Array of resulting query, params and data(string[])
     */
    public static function runFilter($filter_type, $filter_by, $value, $filter_match = 'default')
    {
        $return = [];
        switch ($filter_type) {
            case 'int':
                if ($value === 'null') {
                    return;
                }

                $value = (int)$value;
                $return['query'] = "{$filter_by} = ?";
                $return['param'] = $value;
                $return['data']  = [
                    'title' => $value,
                    'value' => $value,
                ];
            break;

            case 'multi-int':
                if ($value == '') {
                    return;
                }

                $value = explode(',', $value);
                $value = (array)$value;
                $value = array_map('intval', $value);
                $value_txt = implode(',', $value);
                $return['query'] = "{$filter_by} IN ({$value_txt})";
                $return['data']  = [
                    'title' => $value,
                    'value' => $value,
                ];
            break;

            case 'float':
                $value = (float)str_replace(',', '.', $value);
                $return['query'] = "{$filter_by} = ?";
                $return['param'] = $value;
                $return['data']  = [
                    'title' => $value,
                    'value' => $value,
                ];
            break;

            case 'text':
                if ($value[0] == '^' || $filter_match == 'exact') {
                    if ($value[0] == '^') {
                        $value = substr($value, 1);
                    }
                    if ($value[0] == '!') {
                        $value = substr($value, 1);
                        $return['query'] = "{$filter_by} != ?";
                    } else {
                        $return['query'] = "{$filter_by} = ?";
                    }
                    $return['param'] = $value;
                } else {
                    $return['query'] = "{$filter_by}::TEXT ILIKE ?";
                    $return['param'] = '%'.$value.'%';
                }
                $return['data']  = [
                    'title' => $value,
                    'value' => $value,
                ];
            break;

            case 'date':
                $field = $filter_by;
                $start = preg_replace('/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/', '$3-$2-$1', $value);
                if (preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $start)) {
                    $return['query'] = "{$field} >= ? AND {$field} <= ? ";
                    $return['param'] = [strtotime("{$start} 00:00:00"), strtotime("{$start} 23:59:59")];
                    $return['data']  = [
                        'title' => $value,
                        'value' => $value,
                    ];
                }
            break;

            case 'datetime':
                $field = $filter_by;
                $start = preg_replace('/^([0-9]{2})\.([0-9]{2})\.([0-9]{4}) ([0-9]{2}):([0-9]{2})$/', '$3-$2-$1 $4:$5', $value);
                if (preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2})/', $start)) {
                    $return['query'] = "{$field} = ?";
                    $return['param'] = [strtotime("{$start}")];
                    $return['data']  = [
                        'title' => $value,
                        'value' => $value,
                    ];
                }
            break;

            case 'dateinterval':
                $field = $filter_by;
                $start = preg_replace('/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})?.*/', '$3-$2-$1', $value);
                $stop = preg_replace('/.*([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/', '$3-$2-$1', $value);
                if (preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $start) && preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $stop)) {
                    $return['query'] = "{$field} >= ? AND {$field} <= ? ";
                    $return['param'] = [strtotime("{$start} 00:00:00"), strtotime("{$stop} 23:59:59")];
                    $return['data']  = [
                        'title' => $value,
                        'value' => $value,
                    ];
                } elseif (preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $start)) {
                    $return['query'] = "{$field} >= ? AND {$field} <= ? ";
                    $return['param'] = [strtotime("{$start} 00:00:00"), strtotime("{$start} 23:59:59")];
                    $return['data']  = [
                        'title' => $value,
                        'value' => $value,
                    ];
                }
            break;

            case 'sql_date':
                $field = $filter_by;
                $start = preg_replace('/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/', '$3-$2-$1', $value);
                if (preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $start)) {
                    $return['query'] = "{$field} >= ? AND {$field} <= ? ";
                    $return['param'] = ["{$start} 00:00:00", "{$start} 23:59:59"];
                    $return['data']  = [
                        'title' => $value,
                        'value' => $value,
                    ];
                }
            break;
        }

        return $return;
    }
}
