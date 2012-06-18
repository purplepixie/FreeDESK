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
 * Entity Manager class
**/
class EntityManager
{
	/**
	 * FreeDESK instance
	**/
	private $DESK = null;
	
	/**
	 * Constructor
	 * @param mixed &$freeDESK FreeDESK instance
	**/
	function EntityManager(&$freeDESK)
	{
		$this->DESK = &$freeDESK;
	}
	
	/**
	 * Create an entity
	 * @param string $entity Entity ID
	 * @return object Entity object
	**/
	function Create($entity)
	{
		return EntityFactory::Create($this->DESK, $entity);
	}
	
	/**
	 * Load an entity from the database (from a keyfield)
	 * @param string $entity Entity
	 * @param mixed $value Keyfield Value
	 * @return mixed Entity object on success or bool false on failure
	**/
	function Load($entity, $value)
	{
		$table = $this->DESK->DataDictionary->GetTable($entity);
		
		if ($table === false) // no such entity in DD
			return false;
			
		$keyfield = $table->keyfield;
		
		if ($keyfield == "")
			return false; // no keyfield defined in DD
			
		$qb = new QueryBuilder();
		$qb->Add($keyfield, QueryType::Equal, $value);
		
		$q="SELECT * FROM ".$this->DESK->Database->Table($table->entity);
		$q.=" WHERE ".$this->DESK->Database->Clause($qb);
		
		$r=$this->DESK->Database->Query($q);
		
		if ($row=$this->DESK->Database->FetchAssoc($r))
		{
			$entity = $this->Create($entity);
			$entity->LoadAssoc($row);
			$this->DESK->Database->Free($r);
			return $entity;
		}
		
		return false;
	}
	
	/**
	 * Save an entity
	 * @param object &$entity Entity object
	 * @return bool true on success or false on failure
	**/
	function Save(&$entity)
	{
		$eid = $entity->GetEntity();
		$data = $entity->GetData();
		
		$table = $this->DESK->DataDictionary->GetTable($eid);
		
		if ($table === false)
			return false;
		
		$keyfield = $table->keyfield;
		
		if ($keyfield == "" || (!isset($data[$keyfield])))
			return false;
		
		$q="UPDATE ".$this->DESK->Database->Table($table->entity)." SET ";
		
		$first=true;
		foreach($data as $key => $value)
		{
			if ($key != $keyfield)
			{
				if ($first)
					$first=false;
				else
					$q.=",";
				$q.=$this->DESK->Database->Field($key);
				$q.="=";
				$q.=$this->DESK->Database->SafeQuote($value);
			}
		}
		
		$q.=" WHERE ".$this->DESK->Database->Field($keyfield)."=";
		$q.=$this->DESK->Database->SafeQuote($data[$keyfield]);
		
		$this->DESK->Database->Query($q);
		
		return true;
	}
	
	/**
	 * Insert a (new) entity
	 * @param object &$entity Entity object
	 * @return bool true on success or false on failure
	**/
	function Insert(&$entity)
	{
		$eid = $entity->GetEntity();
		$data = $entity->GetData();
		
		$table = $this->DESK->DataDictionary->GetTable($eid);
		
		if ($table === false)
			return false;
		
		$keyfield = $table->keyfield;
		
		$q="INSERT INTO ".$this->DESK->Database->Table($eid);
		
		$fieldlist = array();
		
		foreach($data as $key => $value)
		{
			if (isset($_REQUEST[$key]) &&
				( $key != $keyfield || $_REQUEST[$key]!="" ) )
			{
				$fieldlist[]=$key;
			}
		}
		
		$q.="(";
		$first=true;
		foreach($fieldlist as $field)
		{
			if ($first)
				$first=false;
			else
				$q.=",";
			$q.=$this->DESK->Database->Field($field);
		}
		$q.=")";
		
		$q.=" VALUES(";
		$first=true;
		foreach($fieldlist as $field)
		{
			if ($first)
				$first=false;
			else
				$q.=",";
			$q.=$this->DESK->Database->SafeQuote($_REQUEST[$field]);
		}
		$q.=")";
		
		$this->DESK->Database->Query($q);
		
		return true;
	}
			
				
}
?>
