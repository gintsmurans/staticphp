<?php


/**
 * Returns fixed floating number with precision of $precision. Replaces "," to "." and " " to "".
 *
 * @param $input mixed
 * @param $precision int
 * @return float
 */
function fixFloat($input, $precision = -1)
{
    $input = str_replace([',', ' '], ['.', ''], $input);

    return ($precision === -1 ? (double)$input : round((double)$input, $precision));
}


/**
 * Trim characters, can be used with array_walk
 *
 * @param $value mixed
 * @param $pos int
 * @param $character_mask string Characters to trim
 * @return mixed
 */
function trimChars(&$value, $pos, $character_mask = " \t\n\r\0\x0B")
{
    /*
        Unicode variant:
            $value = preg_replace('/^['.$character_mask.']*(?U)(.*)['.$character_mask.']*$/u', '\\1', $value);
    */
    $value = trim($value, $character_mask);
}


/**
 * Locale specific number_format
 *
 * @param $number int|float
 * @param $decimals int Precision
 * @return float
 */
function localeNumberFormat($number, $decimals = 2)
{
    $locale = localeconv();
    return number_format($number, $decimals, $locale['decimal_point'], $locale['thousands_sep']);
}


/**
 * Returns unique uuid4 value
 *
 * @return string
 */
function uuid4()
{
    $data = openssl_random_pseudo_bytes(16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


function parseQueryString($str, $delimiter = '&')
{
    $op = array();
    $pairs = explode($delimiter, $str);
    foreach ($pairs as $pair) {
        $ex = explode("=", $pair);
        if (count($ex) < 2) {
            continue;
        }
        list($k, $v) = array_map("urldecode", $ex);
        $op[$k] = $v;
    }

    return $op;
}


function weekRange($week, $year = null)
{
    if (empty($year)) {
        $year = date('Y');
    }

    $week -= 1;
    $ts = strtotime("{$year}0104 +{$week} weeks"); // By http://en.wikipedia.org/wiki/ISO_8601#Week_dates
    $start = (date('w', $ts) == 1) ? $ts : strtotime('last monday', $ts);

    return array($start, strtotime('next sunday', $start));
}


function extractArrayByKeys($array, $keys, $required = false, $fill_missing = false)
{
    // Check if input is an array
    if (is_array($array) === false) {
        return false;
    }

    // Build new array
    $new_array = [];
    foreach ($keys as $key) {
        if (isset($array[$key])) {
            $new_array[$key] = $array[$key];
        } else if ($required === true) {
            return false;
        } else if ($fill_missing !== false) {
            $new_array[$key] = $fill_missing;
        }
    }

    // Return new array
    return $new_array;
}


/**
 * Returns a boolean value representing whether any of the passed array elements are empty
 *
 * @return boolean
 */
function anyEmpty($array)
{
    return (count($array) !== count(array_filter($array)));
}


/**
 * Returns a boolean value representing whether all of the passed array elements are empty
 *
 * @return boolean
 */
function allEmpty($array)
{
    return (count(array_filter($array)) === 0);
}


/**
 * Returns string pointing to a (somehow) random temporary filename
 *
 * @return string
 */
function tmpFilename($prefix = 'tmp_', $postfix = '')
{
    $random = uniqid(rand(), true);
    $random = str_replace('.', '_', $random);
    $filename = sys_get_temp_dir().'/'.$prefix.$random.$postfix;
    return $filename;
}
