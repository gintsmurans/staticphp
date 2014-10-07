<?php

/*
|--------------------------------------------------------------------------
| Routing
|
| Each next item overrides current one
| Format: 'regular expression'[without starting slash] => 'new URL'
| Leave '' for default controller
|--------------------------------------------------------------------------
*/

$config['routing'] = array(

    // Default Controller and Method names
    '' => 'home/index',

    // Rest of the routing
    # '^([0-9]+)$' => 'orders/details/$1'  # Example rewrite: http://example.com/1234 -> http://example.com/orders/details/1234
    );

?>