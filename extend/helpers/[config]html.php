<?php

/*
  "StaticPHP Framework" - Simple PHP Framework

    You can use file prefixes:
    1. i: - will be used as inline
    2. b: - link will be prepended with base_url
    3. s: - link will be prepended with site_url
    4. [none] - link will be shown as it is
  
  ---------------------------------------------------------------------------------
  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  any later version.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
  ---------------------------------------------------------------------------------
  
  Copyright (C) 2009  Gints Murāns <gm@gm.lv>
*/


$config['css'] = array(
  '(|home)' => array(
    'i:body{ background: red; }',
  ),
);

$config['js'] = array();

?>