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
 * Data Dictionary Types of Field
**/
abstract class DD_FieldType
{
	/**
	 * Integer
	**/
	const Int = 0;
	/**
	 * Unsigned Int
	**/
	const UnsignedInt = 1;
	/**
	 * Char
	**/
	const Char = 2;
	/**
	 * Float
	**/
	const Float = 3;
	/**
	 * Datetime
	**/
	const DateTime = 4;
	/**
	 * Text (large multi-line block)
	**/
	const Text = 5;
	/**
	 * Password (textual)
	**/
	const Password = 6;
}

/**
 * Data Dictionary Types of Relationship
**/
abstract class DD_RelationshipType
{
	/**
	 * One to One
	**/
	const OTO = 0;
	/**
	 * One to Many
	**/
	const OTM = 2;
	/**
	 * Many to Many
	**/
	const MTM = 3;
}

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
	 * Entity class - for EntityFactory use
	**/
	var $class = "";
	/**
	 * Keyfield
	**/
	var $keyfield = "";
	/**
	 * Entity Directly Editable
	**/
	var $editable = false;
	/**
	 * Add a field to the table
	 * @param mixed $field Field data of type DD_Field
	**/
	function Add($field)
	{
		$this->fields[$field->field] = $field;
		if ($field->keyfield)
			$this->keyfield = $field->field;
	}
	/**
	 * Get a field or return boolean false if not found
	 * @param string $field Name of field
	 * @return mixed False on fail or DD_Field of field
	**/
	function GetField($field)
	{
		if (isset($this->fields[$field]))
			return $this->fields[$field];
		else
			return false;
	}
	
	/**
	 * Set a field value
	 * @param string $field Field name
	 * @param string $item Item/member name
	 * @param mixed $value Value to set item to
	**/
	function SetVal($field, $item, $value)
	{
		if (isset($fields[$field]))
			$fields[$field]->$item = $value;
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
	 * Size (length for char/text or range for int)
	**/
	var $size = 0;
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
	/**
	 * Is a foreign key flag (bool)
	**/
	var $foreignkey = false;
	/**
	 * Binding for foreign key - entity
	**/
	var $foreignentity = "";
	/**
	 * Binding for foreign key - field
	**/
	var $foreignfield = "";
	/**
	 * Is read-only
	**/
	var $readonly = false;
}

/**
 * Data Dictionary Relationship class holds information about inter-entity relationships
**/
class DD_Relationship
{
	/**
	 * Relationship type (1t1, 1tm, m2m)
	**/
	var $type = "";
	/**
	 * First entity
	**/
	var $firstentity = "";
	/**
	 * First field
	**/
	var $firstfield = "";
	/**
	 * Second entity
	**/
	var $secondentity = "";
	/**
	 * Second field
	**/
	var $secondfield = "";
	/**
	 * Link Table (for m2m)
	**/
	var $linktable = "";
	/**
	 * Link table first field (blank uses same name as firstfield)
	**/
	var $linkfirst = "";
	/**
	 * Link table second field (blank uses same name as secondfield)
	**/
	var $linksecond = "";
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
	 * Relationships
	**/
	var $Relationships = array();
	
	/**
	 * Constructor
	 * @param mixed $freeDESK FreeDESK instance
	**/
	function DataDictionary(&$freeDESK)
	{
		$this->DESK = &$freeDESK;
		$this->DESK->PluginManager->Register(new Plugin(
			"Data Dictionary", "0.01", "Core" ));
	}
	
	/**
	 * Add a table
	 * @param mixed $table Table of type DD_Table
	**/
	function AddTable($table)
	{
		$this->Tables[$table->entity] = $table;
		$perm = "entity_".$table->entity;
		$this->DESK->PermissionManager->Register($perm, false);
	}
	
	/**
	 * Get a table
	 * @param string $table Entity name
	 * @return mixed DD_Table if set otherwise bool false
	**/
	function GetTable($table)
	{
		if (isset($this->Tables[$table]))
			return $this->Tables[$table];
		else
			return false;
	}
	
	/**
	 * Add TO a table (add a field to an existing table)
	 * @param string $table Table entity
	 * @param mixed $field Field (of form DD_Field)
	**/
	function AddToTable($table, $field)
	{
		if (isset($this->Tables[$table]))
			$this->Tables[$table]->Add($field);
	}
	
	/**
	 * Set a field value in a table
	 * @param string $table Table entity
	 * @param string $field Field name
	 * @param string $item Item to set
	 * @param mixed $value Value to set item to
	**/
	function SetFieldVal($table, $field, $item, $value)
	{
		if (isset($this->Tables[$table]))
			$this->Tables[$table]->SetVal($field, $item, $value);
	}
	
	/**
	 * Set a table value
	 * @param string $table Table entity
	 * @param string $item Item to set
	 * @param mixed $value Value to set item to
	**/
	function SetTableVal($table, $item, $value)
	{
		if (isset($this->Tables[$table]))
			$this->Tables[$table]->$item = $value;
	}
	
	/**
	 * Add a relationship
	 * @param mixed $relationship Data of type DD_Relationship
	**/
	function AddRelationship($relationship)
	{
		$this->Relationships[] = $relationship;
	}
	
	/**
	 * Add an item - uses RTTI to discover if a table or a relationship
	 * @param mixed $data Data (of type DD_Table or DD_Relationship)
	**/
	function Add($data)
	{
		$class = get_class($data);
		if ($class == "DD_Table")
			$this->AddTable($data);
		else if ($class == "DD_Relationship")
			$this->AddRelationship($data);
		// Otherwise we have no idea...
	}
}


?>
	
