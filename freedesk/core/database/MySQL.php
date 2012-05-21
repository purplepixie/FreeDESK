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
 * MySQL concrete implementation of DatabaseBase
**/

class MySQL extends DatabaseBase
{
	/**
	 * Pointer to the FreeDESK instance
	**/
	private $DESK = null;
	/**
	 * MySQL data connection
	**/
	private $connection = null;

	/**
	 * Table prefix
	**/
	var $prefix = "";

	/**
	 * Constructor
	 * @param object $freeDESK FreeDESK instance
	**/
	function MySQL(&$freekDESK)
	{
		$this->DESK = $freeDESK;
	}
	
	/**
	 * Connect
	 * @param string $server Database server
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $database Database name
	 * @param string $prefix Database table prefix (optonal, default "")
	 * @return bool Successful connection or not
	**/
	function Connect($server, $username, $password, 
		$database, $prefix="")
	{
		$this->prefix = $prefix;
	
		$this->connection = mysql_connect($server, $username, $password);
		if ($this->connection <= 0) return false;
		
		if (!mysql_select_db($database, $ths->connection))
			return false;
			
		return true;
	}
	
	/**
	 * Disconnect
	**/
	function Disconnect()
	{
		mysql_close($this->connection);
	}
	
	/**
	 * Return table name with correct prefix and escaping
	 * @param string $table table un-prefixed
	 * @return string table with prefix and escape
	**/
	function Table($table)
	{
		return "`".$this->prefix.$table."`";
	}
	
	/**
	 * Sanitise user-input using correct escaping
	 * @param string $input user input
	 * @return string Sanitised output
	**/
	function Safe($input)
	{
		return mysql_real_escape($input, $this->connection);
	}
	
	
	/**
	 * Contain a field correctly
	 * @param string $field The field name
	 * @return string Escaped field
	**/
	function Field($field)
	{
		return "`".$field."`";
	}
	
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
	 * @return mixed Results of query
	**/
	function Query($query)
	{
		return mysql_query($query, $this->connection);
	}
	
	/**
	 * Number of rows affected by last query
	 * @return int number of rows affected
	**/
	function RowsAffected()
	{
		return mysql_affected_rows($this->connection);
	}
	
	/**
	 * Number of rows in a result set
	 * @param mixed $result Result set
	 * @return int number of rows in the set
	**/
	function NumRows(&$result)
	{
		return mysql_num_rows($result);
	}
	
	/**
	 * Fetch next associated array from result set
	 * @param mixed $result Result Set
	 * @return array Assocative Array of Results
	**/
	function FetchAssoc(&$result)
	{
		return mysql_fetch_assoc($result);
	}
}

?>
