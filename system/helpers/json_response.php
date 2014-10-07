<?php

/*
|--------------------------------------------------------------------------
| JSON Response
|
| Ease sending JSON response back to browser
|
| Usage:
| json_response($json_data);
| $json_data['xx'] = 1;
|--------------------------------------------------------------------------
*/

function json_response(&$json_data)
{
    static $json_request = false;
    if (empty($json_request))
    {
        header('Content-Type:application/json; charset=utf-8');
        register_shutdown_function(function(&$data){
            $data = reset($data);
            if (!empty($data))
            {
                echo json_encode($data);
            }
        }, array(&$json_data));

        $json_request = true;
    }
}

?>