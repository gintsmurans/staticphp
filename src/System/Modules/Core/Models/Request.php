<?php

namespace System\Modules\Core\Models;

class Request
{
    public static function internal(string $url, array $post = null, array $query = null, bool $https = false): string
    {
        // Create command array
        $cmd_arr = ['php', PUBLIC_PATH . 'index.php'];

        if (!empty($post) && is_array($post)) {
            array_push($cmd_arr, '--post');
            array_push($cmd_arr, http_build_query($post));
        }

        if (!empty($query) && is_array($query)) {
            array_push($cmd_arr, '--query');
            array_push($cmd_arr, http_build_query($query));
        }

        if (!empty($https)) {
            array_push($cmd_arr, '--https');
        }

        array_push($cmd_arr, $url);
        $cmd_arr = array_map('escapeshellarg', $cmd_arr);

        // Prepend script
        array_unshift($cmd_arr, 'LC_ALL=lv_LV.utf8');

        // Implode the command and execute it
        $cmd = implode(' ', $cmd_arr);
        exec($cmd, $output, $return_code);

        return implode("\n", $output);
    }

    public static function httpErrorInData(string $data): string
    {
        $error = stripos($data, '403 Forbidden') !== false;
        $error = $error || stripos($data, '404 Not Found') !== false;
        $error = $error || stripos($data, '500 Internal Server Error') !== false;
        $error = $error || stripos($data, 'syntax error') !== false;

        return $error;
    }
}
