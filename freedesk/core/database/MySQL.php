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
	function MySQL(&$freeDESK)
	{
		$this->DESK = &$freeDESK;
		
		$this->DESK->PluginManager->Register(new Plugin(
			"MySQL Database Engine","0.01","Core","DB" ));
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
		
		if (!mysql_select_db($database, $this->connection))
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
		return mysql_real_escape_string($input, $this->connection);
	}
	
	
	/**
	 * Sanitise user-input string and quote
	 * @param string $input user input
	 * @return string Sanitised quoted output
	**/
	function SafeQuote($input)
	{
		return "\"".$this->Safe($input)."\"";
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
	 * @param bool $report Record any errors using LoggingEngine (optonal, default true)
	 * @return mixed Results of query
	**/
	function Query($query, $report=true)
	{
		$result=mysql_query($query, $this->connection);
		
		if ($report && $this->Error()) // has an error and to be reported
		{
			$err="Query Failed: ".$query;
			$error="SQL Error: ".$this->LastError();
			$this->DESK->LoggingEngine->Log($err, "SQL", "Fail", 1);
			$this->DESK->LoggingEngine->Log($error, "SQL", "Error", 1);
		}
		
		return $result;
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
	
	/**
	 * Free a result set
	 * @param mixed $result Result Set
	**/
	function Free(&$result)
	{
		mysql_free_result($result);
	}
	
	/**
	 * Return an error flag
	 * @return bool Experienced error on last command
	**/
	function Error()
	{
		if (mysql_errno($this->connection)>0)
			return true;
		return false;
	}
	
	/**
	 * Last error code
	 * @return int Error code
	**/
	function ErrorCode()
	{
		return mysql_errno($this->connection);
	}
	
	/**
	 * Last error description
	 * @return string Error description
	**/
	function ErrorDescription()
	{
		return mysql_error($this->connection);
	}
	
	/**
	 * The last inserted ID
	 * @return mixed Last inserted ID
	**/
	function InsertID()
	{
		return mysql_insert_id($this->connection);
	}
	
	/**
	 * Generate a clause from a QueryBuilder object
	 * @param object &$query QueryBuilder object
	 * @return string query string
	**/
	function Clause(&$query)
	{
		$c = "";
		foreach($query->items as $item)
		{
			if (isset($item['field']))
			{
				if ($c!="")
					$c.=" ";
				$c.=$this->Field($item['field']);
				
				switch($item['type'])
				{
					case QueryType::Equal:
						$c.="=";
						break;
					case QueryType::Like:
						$c.=" LIKE ";
						break;
					case QueryType::MoreThan:
						$c.=" > ";
						break;
					case QueryType::MoreThanEqual:
						$c.=" >= ";
						break;
					case QueryType::LessThan:
						$c.=" < ";
						break;
					case QueryType::LessThanEqual:
						$c.=" <= ";
						break;
					case QueryType::NotEqual;
						$c.=" != ";
						break;
				}
				
				$c.=$item['value'];
			}
			else
			{
				switch($item['type'])
				{
					case QueryType::OpenBracket:
						$c.=" ( ";
						break;
					case QueryType::CloseBracket:
						$c.=" ) ";
						break;
					case QueryType::opAND:
						$c.=" AND ";
						break;
					case QueryType::opOR:
						$c.=" OR ";
						break;
				}
			}
		}
		
		if ($c=="")
			$c="1";
		
		return $c;
	}
}

?>
