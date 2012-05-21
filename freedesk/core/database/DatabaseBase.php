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

/**
 * DatabaseBase is the abstract base class for database system implementations
**/

abstract class DatabaseBase
{
	/**
	 * Constructor
	 * @param object $freeDESK FreeDESK instance
	**/
	abstract method DatabaseBase(&$freekDESK);
	
	/**
	 * Connect
	 * @param string $server Database server
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $database Database name
	 * @param string $prefix Database table prefix (optonal, default "")
	**/
	abstract method Connect($server, $username, $password, 
		$database, $prefix="");
	
	/**
	 * Disconnect
	**/
	abstract method Disconnect();
	
	/**
	 * Return table name with correct prefix and escaping
	 * @param string $table table un-prefixed
	 * @return string table with prefix and escape
	**/
	abstract method Table($table);
	
	/**
	 * Sanitise user-input using correct escaping
	 * @param string $input user input
	 * @return string Sanitised output
	**/
	abstract method Safe($input);
	
}

?>
