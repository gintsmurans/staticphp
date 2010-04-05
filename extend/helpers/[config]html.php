<?php

/*
    You can use file prefixes:
    1. i: - will be used as inline
    2. b: - link will be prepended with base_url
    3. s: - link will be prepended with site_url
    4. [none] - link will be shown as it is
*/

$config['css'] = array(
  '(|home)' => array(
    'i:body{ background: red; }',
  ),
);

$config['js'] = array();

?>