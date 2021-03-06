<?php
/* -------------------------------------------------------------
This file is part of FreeDESK

FreeDESK is (C) Copyright 2012 David Cutting

FreeDESK is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

FreeDESK is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with FreeDESK.  If not, see www.gnu.org/licenses

For more information see www.purplepixie.org/freedesk/
-------------------------------------------------------------- */

// This file will create mysql dumps (Linux only) of the database into $output

global $dbdrop, $dbuser, $dbpass, $dbdata, $output;
if (!isset($dbdrop)) $dbdrop=false;

if ($dbdrop)
exec("/usr/bin/mysqldump -u ".$dbuser." -p".$dbdata." --add-drop-table -n -d ".$dbdata, $output);
else
exec("/usr/bin/mysqldump -u ".$dbuser." -p".$dbdata." --skip-add-drop-table -n -d ".$dbdata, $output);


?>
