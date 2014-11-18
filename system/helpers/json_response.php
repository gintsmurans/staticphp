<?php

/**
 * Ease sending JSON response back to browser
 *
 * @example Call function: <code>json_response($json_data);</code> add some data: <code>$json_data['xx'] = 1;</code>
 *          and on the end of script execution the $json_data array will be sent to client along with content-type:application/json header.
 * @access public
 * @param mixed &$json_data
 * @return void
 */
function json_response(&$json_data)
{
    static $json_request = false;
    if (empty($json_request))
    {
        header('Content-Type:application/json; charset=utf-8');
        register_shutdown_function(function (&$data) {
            $data = reset($data);
            if (!empty($data))
            {
                echo json_encode($data);
            }
        }, [&$json_data]);

        $json_request = true;
    }
}
