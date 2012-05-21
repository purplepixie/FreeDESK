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
 * Data Dictionary Table class holds information about a table
**/
class DD_Table
{
	/**
	 * Table name (human form)
	**/
	var $name = "";
	/**
	 * Table entity name (database)
	**/
	var $entity = "";
	/**
	 * Fields
	**/
	var $fields = array();
	/**
	 * Add a field to the table
	 * @param mixed $field Field data of type DD_Field
	**/
	function Add($field)
	{
		$fields[$field->field] = $field;
	}
}

/**
 * Data Dictionary Field class holds information about a field
**/
class DD_Field
{
	/**
	 * Field name (human)
	**/
	var $name = "";
	/**
	 * Field column name (database)
	**/
	var $field = "";
	/**
	 * Field type (int, char, text, datetime)
	**/
	var $type = "";
	/**
	 * Searchable (bool)
	**/
	var $searchable = false;
	/**
	 * Keyfield flag (bool)
	**/
	var $keyfield = false;
	/**
	 * Display in standard results flag (bool)
	**/
	var $display = true;
}

/**
 * DataDictionary class for FreeDESK - holds all DD information for the system
**/
class DataDictionary
{
	/**
	 * FreeDESK instance
	**/
	private $DESK = null;
	/**
	 * Tables
	**/
	var $Tables = array();
	
	/**
	 * Constructor
	 * @param mixed $freeDESK FreeDESK instance
	**/
	function DataDictionary(&$freeDESK)
	{
		$this->DESK = $freeDESK;
	}
	
	/**
	 * Add a table
	 * @param mixed $table Table of type DD_Table
	**/
	function Add($table)
	{
		$ths->Tables[$table->entity] = $table;
	}
}


?>
	
