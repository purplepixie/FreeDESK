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
 * Main FreeDESK class contains all sub-classes
**/
class FreeDESK
{
	/**
	 * Major and Minor Version
	**/
	private var $majorVersion = 0.00;
	/**
	 * Patch Level
	**/
	private var $patchVersion = 0;
	/**
	 * Get the full compound version
	 * @return string Compound version
	**/
	function Version()
	{
		return $majorVersion.".".$patchVersion;
	}
	
	// Component Class Instances
	
	/**
	 * Configuration Class
	**/
	var $Configuration = null;
	
	/**
	 * Logging engine
	**/
	var $LoggingEngine = null;
	
	/**
	 * Plugin Manager
	**/
	var $PluginManager = null;
	
	/**
	 * Database
	**/
	var $Database = null;
	
	/**
	 * Entity Manager
	**/
	var $EntityManager = null;
	
	/**
	 * Data Dictionary
	**/
	var $DataDictionary = null;
	
	/**
	 * Request Manager
	**/
	var $RequestManager = null;
	
	/**
	 * Context Manager
	**/
	var $ContextManager = null;
	
	/**
	 * Include System
	**/
	var $Include = null;
	
	// Methods and Processes in Main FreeDESK class
	
	/**
	 * Constructor for FreeDESK
	 * @param string $baseDir Base directory of the system (file root, can be relative)
	**/
	function FreeDESK($baseDir)
	{
		// First include and instantiate the include system for all further includes
		require($baseDir."core/Include.php")
			or die("Cannot open core/Include.php - fatal error");
		$this->Include = new Include($this, $baseDir);
		
	}
	
}
?>
