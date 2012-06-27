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
 * Plugin class - holds all associated data with generic plugins
**/
class Plugin
{
	/**
	 * Name
	**/
	var $name;
	/**
	 * Version (maj.min only)
	**/
	var $version;
	/**
	 * Type
	**/
	var $type="";
	/**
	 * Sub-Type
	**/
	var $subtype="";
	/**
	 * Class Name
	**/
	var $classname="";
	/**
	 * Constructor
	 * @param string $name Name of plugin (optional, default "")
	 * @param string $version Version of plugin (optional, default "")
	 * @param string $type Type of plugin (optional, default "")
	 * @param string $subtype Subtype of plugin (optional, default "")
	 * @param string $classname Class name of pluging (optional, default "")
	**/
	function Plugin($name="",$version="",$type="",$subtype="",$classname="")
	{
		$this->name=$name;
		$this->version=$version;
		$this->type=$type;
		$this->subtype=$subtype;
		$this->classname=$classname;
	}
}

/**
 * Plugin Manager - Handles all run-time-enabled plugins
**/
class PluginManager
{
	/**
	 * FreeDESK instance
	**/
	private $DESK = null;
	
	/**
	 * Array of registered plugins and components
	**/
	private $plugins = array();
	
	/**
	 * Array of simple pages
	**/
	private $pages = array();
	
	/**
	 * Constructor
	 * @param mixed $freeDESK FreeDESK instance
	**/
	function PluginManager(&$freeDESK)
	{
		$this->DESK = &$freeDESK;
	}
	
	/**
	 * Register a plugin
	 * @param mixed $plugin Plugin to register
	**/
	function Register(&$plugin)
	{
		$this->plugins[]=$plugin;
	}
	
	/**
	 * Register a page for display
	 * @param string $id Page identifier
	 * @param string $page Page path (fully-qualified)
	 * @param bool $autoperm Automatically register permission for the page (optional, default true)
	**/
	function RegisterPage($id, $page, $autoperm=true)
	{
		$this->pages[$id]=$page;
		$this->DESK->PermissionManager->Register("page.".$id,false);
	}
	
	/**
	 * Get a page by ID
	 * @param string $id Page identifier
	 * @return mixed Page path (fully-qualified) or bool false on failure
	**/
	function GetPage($id)
	{
		if (isset($this->pages[$id]))
			return $this->pages[$id];
		return false;
	}
	
	/**
	 * Return all plugins
	 * @return array Array of all plugins
	**/
	function GetAll()
	{
		return $this->plugins;
	}
	
	/**
	 * Return plugins by type
	 * @param string $type Plugin Type
	 * @return array List of plugins
	**/
	function GetType($type)
	{
		$output=array();
		foreach($this->plugins as $plugin)
		{
			if ($plugin->type == $type)
				$output[]=$plugin;
		}
		return $output;
	}
	
}


?>
