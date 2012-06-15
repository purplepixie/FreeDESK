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
 * Database Query Type
**/
abstract class QueryType
{
	const Equal = 0;
	const Like = 1;
	const MoreThan = 2;
	const MoreThanEqual = 3;
	const LessThan = 4;
	const LessThanEqual = 5;
	const NotEqual = 6;
	
	const OpenBracket = 100;
	const CloseBracket = 101;
	
	const opAND = 200;
	const opOR = 201;
}

/**
 * Query Builder Class
**/
class QueryBuilder
{
	/**
	 * Query items array
	**/
	var $items = array();
	
	/**
	 * Limit Flag
	**/
	var $limit = false;
	
	/**
	 * Start (for limit)
	**/
	var $start = 0;
	
	/**
	 * Entries (for limit)
	**/
	var $entries = 30;
	
	/**
	 * Order Flag
	**/
	var $order = false;
	
	/**
	 * Order fields
	**/
	var $orderlist = array();
	
	/**
	 * Add item
	 * @param string $field Field
	 * @param mixed $type QueryType const
	 * @param mixed $value Value
	**/
	function Add($field, $type, $value)
	{
		$this->items[] = array(
			"field" => $field,
			"type" => $type,
			"value" => $value );
	}
	
	/**
	 * Add an order field
	 * @param string $field Field
	 * @param bool $asc Ascending (optional, default true) - false is descending
	**/
	function AddOrder($field, $asc = true)
	{
		if (!$this->order)
			$this->order = true;
		$this->orderlist[$field]=$asc;
	}
	
	/**
	 * Open bracket
	**/
	function OpenBracket()
	{
		$this->items[] = array("type" => QueryType::OpenBracket);
	}
	
	/**
	 * Close bracket
	**/
	function CloseBracket()
	{
		$this->items[] = array("type" => QueryType::CloseBracket);
	}
	
	/**
	 * Add operation
	 * @param mixed $operation Op of type QueryType
	**/
	function AddOperation($operation)
	{
		$this->items[] = array("type" => $operation);
	}
}


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
	 * Sanitise user-input string and quote
	 * @param string $input user input
	 * @return string Sanitised quoted output
	**/
	abstract function SafeQuote($input);
	
	
	
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
		return $this->ErrorCode().": ".$this->ErrorDescription();
	}
	
	/**
	 * The last inserted ID
	 * @return mixed Last inserted ID
	**/
	abstract function InsertID();
	
	/**
	 * Generate a clause from a QueryBuilder object
	 * @param object &$query QueryBuilder object
	 * @return string query string
	**/
	abstract function Clause(&$query);
	
}

?>
