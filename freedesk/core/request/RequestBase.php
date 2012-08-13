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
 * Abstract Request base class for all request classes
**/
abstract class RequestBase
{
	/**
	 * Current FreeDESK instance
	**/
	protected $DESK = null;
	
	/**
	 * Current Request ID
	**/
	var $ID = 0;
	
	/**
	 * Entity Data
	**/
	protected $Entity = null;
	
	/**
	 * Updates
	**/
	protected $Updates = null;
	
	/**
	 * Constructor
	 * @param mixed &$freeDESK Current FreeDESK instance
	**/
	function RequestBase(&$freeDESK)
	{
		$this->DESK = &$freeDESK;
	}
	
	/**
	 * Create a request
	 * @param int $customer Customer ID
	 * @param string $update Initial Update
	 * @param int $class Request Class
	 * @param int $status Request status
	 * @param int $priority Priority Code (optional, default 0)
	 * @param int $group Request Group (optional, default 0)
	 * @param string $assign Assigned user (optional, default "")
	 * @return string Request ID
	**/
	abstract function Create($customer, $update, $class, $status, $priority=0, $group=0, $assign="");
	
	/**
	 * Update a request
	 * @param string $update Update text
	 * @param bool $public Public update (optional, default false)
	**/
	abstract function Update($update, $public=false);
	
	/**
	 * Change a request status
	 * @param int $status New status
	 * @param bool $public Public update (optional, default false)
	**/
	abstract function Status($status, $public=false);
	
	/**
	 * Assign a request
	 * @param int $group Group ID
	 * @param string $username Username (optional, default "")
	 * @param bool $public Public update (optional, default false)
	**/
	abstract function Assign($group, $username="", $public=false);
	
	/**
	 * Attach a file
	 * @param int $fileid File ID
	 * @param bool $public Public update (optional, default false)
	**/
	abstract function Attach($fileid, $public=false);
	
	/**
	 * Output XML
	 * @return string xml output
	 * @param bool $header Put XML header on output (optional, default false)
	**/
	abstract function XML($header=false);
	
	/**
	 * Set an entity value
	 * @param string $field Field ID
	 * @param string $value Value
	**/
	function Set($field, $value)
	{
		if ($this->Entity == null)
			$this->Entity = new Entity($this->DESK);
		$this->Entity->Set($field, $value);
	}
	
	/**
	 * Get an entity value
	 * @param string $field Field ID
	 * @return mixed Value
	**/
	function Get($field)
	{
		$data = $this->Entity->GetData();
		if (isset($data[$field]))
			return $data[$field];
		else
			return false;
	}
	
	/**
	 * Load updates for our ID
	**/
	function LoadUpdates()
	{
		$q="SELECT * FROM ".$this->DESK->Database->Table("update")." WHERE ";
		$q.=$this->DESK->Database->Field("requestid")."=".$this->DESK->Database->Safe($this->ID)." ";
		$q.="ORDER BY ".$this->DESK->Database->Field("updateid")." DESC";
		
		$this->Updates = array();
		
		$r=$this->DESK->Database->Query($q);
		
		while ($row=$this->DESK->Database->FetchAssoc($r))
			$this->Updates[] = $row;
		
		$this->DESK->Database->Free($r);
	}
	
	/**
	 * Get updates
	 * @return array Updates
	**/
	function GetUpdates()
	{
		if ($this->Updates == null)
			$this->LoadUpdates();
		return $this->Updates;
	}
		
}
?>
