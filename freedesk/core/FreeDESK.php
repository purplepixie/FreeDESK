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
	private $majorVersion = "0.00";
	/**
	 * Patch Level
	**/
	private $patchVersion = 0;
	/**
	 * Release level flag (a b or blank)
	**/
	private $releaseFlag = "a";
	/**
	 * Get the full compound version
	 * @return string Compound version
	**/
	function Version()
	{
		return $this->majorVersion.".".$this->patchVersion;
	}
	/**
	 * Get the full compound version with release level flag
	 * @return string Full compound version with release flag
	**/
	function FullVersion()
	{
		return $this->Version().$this->releaseFlag;
	}
	
	// Component Class Instances
	
	/**
	 * Configuration Class
	**/
	var $Configuration = null;
	
	/**
	 * Basic Configuration
	**/
	var $BaseConfig = null;
	
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
		require($baseDir."core/IncludeManager.php");
			//or die("Cannot open core/IncludeManager.php - fatal error");
		$this->Include = new IncludeManager($this, $baseDir);
		
		// Now the basic configuration
		$this->BaseConfig = $this->Include->IncludeInstance("config/Config.php","FreeDESK_Configuration",false);
		
		// Plugin Manager
		$this->PluginManager = $this->Include->IncludeInstance("core/PluginManager.php","PluginManager");
		
		// Register Ourselves
		$core = new Plugin();
		$core->name="FreeDESK Core";
		$core->version=$this->Version();
		$core->type="Core";
		$this->PluginManager->Register($core);
		
		// Database Engine
		// First include the base class
		$this->Include->IncludeFile("core/database/DatabaseBase.php");
		// Now the concrete class
		$this->Database = $this->Include->IncludeInstance("core/database/".$this->BaseConfig->db_System.".php",
			$this->BaseConfig->db_System);
			
		// Configuration Manager
		$this->Configuration = $this->Include->IncludeInstance("core/Configuration.php","Configuration");
		
		// Logging Engine
		$this->LoggingEngine = $this->Include->IncludeInstance("core/LoggingEngine.php", "LoggingEngine");
		
		// Data Dictionary
		$this->DataDictionary = $this->Include->IncludeInstance("core/DataDictionary.php", "DataDictionary");
		$this->Include->IncludeExec("config/DD.php","FreeDESK_DD"); // Core DD
		
		// Context Manager
		$this->ContextManager = $this->Include->IncludeInstance("core/ContextManager.php", "ContextManager");
		
	}
	
	/**
	 * Start the FreeDESK system, will connect to the database and load configuration
	 * @return bool True on successful start otherwise false on failure
	**/
	function Start()
	{
		// Connect to the database
		if (!$this->Database->Connect($this->BaseConfig->db_Server, $this->BaseConfig->db_Username,
			$this->BaseConfig->db_Password, $this->BaseConfig->db_Database, $this->BaseConfig->db_Prefix))
			return false;
		// Load system configuration
		$this->Configuration->Load();
		// Logging Engine
		$this->LoggingEngine->Start();
		$this->LoggingEngine->Log("FreeDESK Startup ".$this->FullVersion(),"Core","Start",10);
		
		return true;
	}
	
	/**
	 * Stop the FreeDESK system - disconnect from the database
	**/
	function Stop()
	{
		//
	}
	
}
?>
