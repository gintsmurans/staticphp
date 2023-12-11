<?php

namespace System\Modules\Utils\Models;

class Url
{
    /**
     * Ensure there is a ending slash.
     *
     * @param string $string String to append slash to
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function ensureSlash(string $string)
    {
        if (strlen($string) > 0 && $string[-1] != '/') {
            $string .= '/';
        }

        return $string;
    }

    /**
     * Add url part
     *
     * @param string $url  Existing url
     * @param string $part Part to add
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function addTo(string $url, string $part)
    {
        return "{$url}/$part";
    }

    /**
     * Join paths
     *
     * @param array $parts Parts of url to join
     *
     * @access public
     * @static
     *
     * @return string
     */
    public static function join(array $parts)
    {
        $parts = array_map(
            function ($item) {
                return trim($item, '/');
            },
            $parts
        );
        return implode('/', $parts);
    }
}
