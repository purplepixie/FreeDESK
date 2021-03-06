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
 * LogMethodDB implements database-level logging
**/
class LogMethodDB extends LogMethodBase
{
	/**
	 * FreeDESK instance
	**/
	private $DESK = null;

	/**
	 * Constructor
	 * @param mixed $freeDESK FreeDESK instance
	**/
	function LogMethodDB(&$freeDESK)
	{
		$this->DESK = &$freeDESK;
		$this->DESK->PluginManager->Register(new Plugin(
			"Core Database Logging", "0.01", "Log", "Database", "LogMethodDB" ));
	}

	/**
	 * Log an event
	 * @param string $event Event description
	 * @param string $class Event class
	 * @param string $type Event type (optional, default "")
	 * @param int $level Event level (0 = fatal, 10 = low priority info only) (optional, default 10)
	**/
	function Log($event, $class, $type="", $level=10)
	{
		if (strlen($event)>254)
			$event=substr($event,0,251)."...";
		$q="INSERT INTO ".$this->DESK->Database->Table("syslog")."(".$this->DESK->Database->Field("event_dt").",";
		$q.=$this->DESK->Database->Field("event").",".$this->DESK->Database->Field("event_class").",";
		$q.=$this->DESK->Database->Field("event_type").",".$this->DESK->Database->Field("event_level").") ";
		$q.="VALUES(NOW(),\"".$this->DESK->Database->Safe($event)."\",\"".$this->DESK->Database->Safe($class)."\",";
		$q.="\"".$this->DESK->Database->Safe($type)."\",".$this->DESK->Database->Safe($level).")";
		
		
		// Log into DB ensure report flag is false to avoid infinite loop if SQL error in this method
		$this->DESK->Database->Query($q, false);
	}
	
}

?>
