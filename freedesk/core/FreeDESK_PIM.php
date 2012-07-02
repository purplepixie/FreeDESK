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
 * FreeDESK_PIM is the abstacr base class for all PIM components
**/
abstract class FreeDESK_PIM
{
	/**
	 * FreeDESK instance
	**/
	protected $DESK = null;
	
	/**
	 * File path for PIM
	**/
	protected $filepath = "";
	
	/**
	 * Web path for PIM
	**/
	protected $webpath = "";
	
	/**
	 * Main Constructor
	 * @param mixed $freeDESK FreeDESK instance
	 * @param string $filepath Path to plugin directory (filebase)
	 * @param string $webpath Path to plugin directory (webpath)
	**/
	function FreeDESK_PIM(&$freeDESK, $filepath, $webpath)
	{
		$this->DESK = &$freeDESK;
		$this->filepath = $filepath;
		$this->webpath = $webpath;
	}
	
	/**
	 * Install - to be overriden
	**/
	function Install()
	{
		//
	}
	
	/**
	 * Activate - to be overriden
	**/
	function Activate()
	{
		//
	}
	
	/**
	 * De-Activate - to be overriden
	**/
	function Deactivate()
	{
		//
	}
	
	/**
	 * Uninstall - to be overriden
	**/
	function Uninstall()
	{
		//
	}
	
	/**
	 * API Call - to be overriden
	 * @param string $mode API Mode
	**/
	function API($mode)
	{
		//
	}
	
	/**
	 * Event Call - to be overriden
	 * @param string $event Event
	 * @param mixed &$data Event data (dependent on the event)
	**/
	function Event($event, &$data)
	{
		//
	}
	
	/**
	 * Static exec/registration function to list provided interfaces
	 * @param mixed $DESK Reference to current FreeDESK instance
	**/
	static function Exec(&$DESK)
	{
		//
	}
	
}
?>
