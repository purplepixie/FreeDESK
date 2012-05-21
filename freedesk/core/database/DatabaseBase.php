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
	//abstract function DatabaseBase(&$freekDESK);
	
	/**
	 * Connect
	 * @param string $server Database server
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $database Database name
	 * @param string $prefix Database table prefix (optonal, default "")
	 * @return bool Successful connection or not
	**/
	abstract function Connect($server, $username, $password, 
		$database, $prefix="");
	
	/**
	 * Disconnect
	**/
	abstract function Disconnect();
	
	/**
	 * Return table name with correct prefix and escaping
	 * @param string $table table un-prefixed
	 * @return string table with prefix and escape
	**/
	abstract function Table($table);
	
	/**
	 * Sanitise user-input using correct escaping
	 * @param string $input user input
	 * @return string Sanitised output
	**/
	abstract function Safe($input);
	
	
	/**
	 * Contain a field correctly
	 * @param string $field The field name
	 * @return string Escaped field
	**/
	abstract function Field($field);
	
	/**
	 * Escape and contain a field correctly
	 * @param string $value The value of the field
	 * @return string Escaped and prefixed+suffixed data
	**/
	function FieldSafe($value)
	{
		return $this->Field($this->Safe($value));
	}
	
	/**
	 * Perform a query
	 * @param string $query SQL query
	 * @param bool $report Record any errors using LoggingEngine (optonal, default true)
	 * @return mixed Results of query
	**/
	abstract function Query($query, $report=true);
	
	/**
	 * Number of rows affected by last query
	 * @return int number of rows affected
	**/
	abstract function RowsAffected();
	
	/**
	 * Number of rows in a result set
	 * @param mixed $result Result set
	 * @return int number of rows in the set
	**/
	abstract function NumRows(&$result);
	
	/**
	 * Fetch next associated array from result set
	 * @param mixed $result Result Set
	 * @return array Assocative Array of Results
	**/
	abstract function FetchAssoc(&$result);
	
	/**
	 * Free a result set
	 * @param mixed $result Result Set
	**/
	abstract function Free(&$result);
	
	/**
	 * Return an error flag
	 * @return bool Experienced error on last command
	**/
	abstract function Error();
	
	/**
	 * Last error code
	 * @return int Error code
	**/
	abstract function ErrorCode();
	
	/**
	 * Last error description
	 * @return string Error description
	**/
	abstract function ErrorDescription();
	
	/**
	 * Details of the last error
	 * @return string Code and error description
	**/
	function LastError()
	{
		return $this->ErrorCode.": ".$this->ErrorDescription();
	}
}

?>
