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
 * LoggingEngine class - handles all the logging events for the system
**/
class LoggingEngine
{
	/**
	 * FreeDESK instance
	**/
	private $DESK = null;
	/**
	 * Logging Level
	**/
	private $loglevel = 10;
	/**
	 * Concrete Methods
	**/
	private $methods = array();
	/**
	 * Configured types
	**/
	private $types = array();
	
	/**
	 * Constructor
	 * @param mixed $freeDESK FreeDESK instance
	**/
	function LoggingEngine(&$freeDESK)
	{
		$this->DESK=&$freeDESK;
		$this->DESK->Include->IncludeFile("core/LogMethodBase.php");
		$this->DESK->PluginManager->Register(new Plugin(
			"Logging Engine", "0.01", "Core" ));
			
		// Now we include the shipped standard logging method(s)
		$this->methods['LogMethodDB'] = $this->DESK->Include->IncludeInstance("core/logging/LogMethodDB.php","LogMethodDB");
	}
	
	/**
	 * Start logging engine
	**/
	function Start()
	{
		$this->loglevel = $this->DESK->Configuration->Get("log.level",10);
		
		
		// And get a list of the currently configured logging options
		$logopts = $this->DESK->Configuration->Get("log.types","LogMethodDB");
		$logopts = explode(":",$logopts);
		
		// Now set the required logging methods
		foreach($logopts as $logtype)
		{
			if (isset($this->methods[$logtype])) // Does it exist?
				$this->types[] = $logtype;
		}
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
		if ($level > $this->loglevel) // Ignore
			return false;
		foreach($this->types as $method)
			$this->methods[$method]->Log($event, $class, $type, $level);
	}

}


?>
