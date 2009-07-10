<?php
/*
    "StaticPHP Framework" - Little PHP Framework

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

    Copyright (C) 2009  Gints Murāns <gm@mstuff.org>
*/



// Routing, each next item overrides current one
// format: 'regular expression' => '[controller directory / ]controller class name / method name'
// Leave first one for default controller
$config['routing'] = array(

  // Default Controller and Method names
  '' => 'language/index',

  // Rest of the routing
  
);



?>