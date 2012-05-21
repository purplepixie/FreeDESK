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
 * Main System-Wide Configuration Class (uses sysconfig table)
**/
class Configuration
{
	/**
	 * Configuration items
	**/
	private $items = array();
	/**
	 * FreeDESK instance
	**/
	private $DESK = null;
	
	/**
	 * Constructor
	**/
	function Configuration(&$freeDESK)
	{
		$this->DESK = $freeDESK;
	}
	
	/**
	 * Load Configuration
	**/
	function Load()
	{
		$q = "SELECT * FROM ".$this->DESK->Database->Table("sysconfig");
		$r = $this->DESK->Database->Query($q);
		while ($i = $this->DESK->Database->FetchAssoc($r))
		{
			$this->items[$i['sc_option']] = $i['sc_value'];
		}
		$this->DESK->Database->Free($r);
	}
	
	/**
	 * Get a configuration item
	 * @param string $name Configuration option
	 * @param string $default Default return if not found (optional, default "")
	 * @return string Option value or default if not found
	**/
	function Get($name, $default="")
	{
		if (!isset($this->items[$name]))
			return $default;
		else
			return $this->items[$name];
	}
	
	/**
	 * Set a configuration item (no security check in here)
	 * @param string $name Configuration option
	 * @param string $value Option value
	 * @param bool $persist Persistant (saved to database), optional default=true
	**/
	function Set($name, $value, $persist=true)
	{
		$items[$name]=$value;
		
		if ($persist)
		{
			$q="SELECT * FROM ".$this->DESK->Database->Table("sysconfig")." WHERE ";
			$q.=$this->DESK->Database->Field("sc_option")."=\"".$this->DESK->Database->Safe($name)."\"";
			$r=$this->DESK->Database->Query($q);
			if ($this->DESK->Database->NumRows($r)>0)
			{
				// Already exists in database
				$q="UPDATE ".$this->DESK->Database->Table("sysconfig")." SET ";
				$q.=$this->DESK->Database->Field("sc_value")."=\"";
				$q.=$this->DESK->Database->Safe($value)."\" WHERE ".$this->DESK->Database->Field("sc_option")."=";
				$q.="\"".$this->DESK->Database->Safe($name)."\"";
			}
			else
			{
				// New Entry
				$q="INSERT INTO ".$this->DESK->Database->Table("sysconfig")."(".$this->DESK->Database->Field("sc_option").",";
				$q.=$this->DESK->Database->Field("sc_value").") VALUES(\"".$this->DESK->Database->Safe($name)."\",";
				$q.="\"".$this->DESK->Database->Safe($value)."\")";
			}
			$this->DESK->Database->Query($q);
		}
	}
	
	/**
	 * Delete a configuration item (no security check in here)
	 * @param string $name Configuration option
	**/
	function Delete($name)
	{
		$q="DELETE FROM ".$this->DESK->Database->Table("sysconfig")." WHERE ".$this->DESK->Database->Field("sc_option")."=\"";
		$q.=$this->DESK->Database->Safe($name)."\" LIMIT 0,1";
		$this->DESK->Database->Query($q);
	}
	

}

?>
