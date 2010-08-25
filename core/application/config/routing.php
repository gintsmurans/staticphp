<?php

// Routing, each next item overrides current one
// format: 'current URI - regular expression'[without starting slash] => 'new URI - regular expression'
// Leave first one for default controller
$config['routing'] = array(

  // Default Controller and Method names
  '' => 'home/index',
  
  // Rest of the routing
);

?>