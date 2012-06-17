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
 * Entity base is the entity base class
**/
abstract class EntityBase
{
/**
 * FreeDESK instance
**/
protected $DESK = null;

/**
 * Data Elements
**/
protected $data = array();

/**
 * Entity (table name)
**/
protected $entity = "";

/**
 * Data dictionary entry for entity
**/
protected $table = null;

/**
 * Constructor
 * @param mixed $freeDESK FreeDESK instance
**/
function EntityBase(&$freeDESK)
{
	$this->DESK = &$freeDESK;
}

/**
 * Set a data field to a value
 * @param string $field Field ID
 * @param string $data Data
**/
function Set($field, $data)
{
	$this->data[$field]=$data;
}

/**
 * Load from an asociative array
 * @param array &$assoc Array
**/
function LoadAssoc(&$assoc)
{
	foreach($assoc as $key => $val)
		$this->Set($key, $val);
}

/**
 * Set the entity
 * @param string $entity Entity
**/
function SetEntity($entity)
{
	$this->entity = $entity;
}

/**
 * Get entity
 * @return string Entity
**/
function GetEntity()
{
	return $this->entity;
}

/**
 * Output a data array as XML
 * @param mixed &$xml XML creator
 * @param array &$data Data object
 * @return mixed XML creator
**/
protected function XMLData(&$xml)
{
	//TODO: Deal with entity lists
	
	foreach($this->data as $key=>$item)
	{
		if (is_object($item))
		{
			if (is_subclass_of($item, "EntityBase"))
			{
				$subdata=array("entity"=>$key);
				$xml->startElement("subentity",$subdata);
				$xml->charData($item->XML());
				$xml->endElement("subentity");
			}
			else if (get_class($data) == "EntityList")
			{
				// TODO
			}
		}
		else
		{
			$head=array("field"=>$key);
			$xml->startElement("field",$head);
			$xml->charData($item, true);
			$xml->endElement("field");
		}
	}
	return $xml;
}

/**
 * Output as XML - can be overriden
 * @param bool $header Put an XML header in (optional, default false)
 * @return string XML for data
**/
function XML($header=false)
{
	$xml = new xmlCreate();
	$data=array("entity"=>$this->entity);
	$xml->startElement("entity",$data);
	$this->XMLData($xml);
	$xml->endElement("entity");
	
	return $xml->getXML($header);
}


/**
 * Get all the raw data
 * @return array Data array
**/
function GetData()
{
	return $this->data;
}


}

?>

