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
	 * Array of plugin modules
	**/
	private $pims = array();
	
	/**
	 * PIM Counter
	**/
	private $pim_counter = 0;
	
	/**
	 * Array of simple pages
	**/
	private $pages = array();
	
	/**
	 * Array of PIM pages
	**/
	private $pim_pages = array();
	
	/**
	 * Array of scripts
	**/
	private $scripts = array();
	
	/**
	 * Array of CSS
	**/
	private $css = array();
	
	/**
	 * Array of API modes for PIMS
	**/
	private $pim_api = array();
	
	/**
	 * Installed PIM List
	**/
	private $installed_pims = null;
	
	/**
	 * Constructor
	 * @param mixed $freeDESK FreeDESK instance
	**/
	function PluginManager(&$freeDESK)
	{
		$this->DESK = &$freeDESK;
		$this->DESK->PermissionManager->Register("sysadmin_plugins",false);
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
		if ($autoperm)
			$this->DESK->PermissionManager->Register("page.".$id,false);
	}
	
	/**
	 * Register a PIM page for use in a PIM
	 * @param string $page Page identifier
	 * @param int $id PIM internal ID
	**/
	function RegisterPIMPage($page, $id)
	{
		$this->pim_pages[$page] = $id;
	}
	
	/**
	 * Register a script for inclusion
	 * @param string $script Web path to script
	**/
	function RegisterScript($script)
	{
		$this->scripts[] = $script;
	}
	
	/**
	 * Register CSS for inclusion (after Skin CSS)
	 * @param string $css Web path to CSS
	**/
	function RegisterCSS($css)
	{
		$this->css[]=$css;
	}
	
	/**
	 * Register an API call for inclusion
	 * @param string $mode API Mode
	 * @param int $id Plugin ID
	**/
	function RegisterPIMAPI($mode, $id)
	{
		$this->pim_api[$mode] = $id;
	}
	
	/**
	 * Perform an API call
	 * @param string $mode API Call
	 * @return bool False if no API call made true if called
	**/
	function API($mode)
	{
		if (isset($this->pim_api[$mode]))
			if (isset($this->pims[$this->pim_api[$mode]]))
			{
				$this->pims[$this->pim_api[$mode]]->API($mode);
				return true;
			}
		return false;
	}
	
	/**
	 * Get list of scripts
	 * @return array List of scripts
	**/
	function GetScripts()
	{
		return $this->scripts;
	}
	
	/**
	 * Get list of CSS
	 * @return array List of CSS
	**/
	function GetCSS()
	{
		return $this->css;
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
	 * Call a PIM Page
	 * @param $id Page identifier
	 * @return bool true if exists and called or false if not
	**/
	function PIMPage($id)
	{
		if (isset($this->pim_pages[$id]))
		{
			$pimid = $this->pim_pages[$id];
			if (isset($this->pims[$pimid]))
			{
				$this->pims[$pimid]->Page($id);
				return true;
			}
		}
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
	
	/**
	 * Load a PIM
	 * @param string $pim Plugin Module Directory
	**/
	function LoadPIM($pim)
	{
		$filepath = "plugins/".$pim."/";
		$webpath = "plugins/".$pim."/";
		$id = $this->pim_counter++;
		
		include($filepath.$pim.".php");
		
		$this->pims[$id] = new $pim($this->DESK, $filepath, $webpath, $id);
		$this->pims[$id]->Start();
	}
	
	/**
	 * Load installed PIM list
	**/
	private function LoadInstalledPIMS()
	{
		$q="SELECT * FROM ".$this->DESK->Database->Table("plugins");
		$r=$this->DESK->Database->Query($q);
		
		$this->installed_pims = array();
		
		while ($row = $this->DESK->Database->FetchAssoc($r))
		{
			$this->installed_pims[$row['plugin']] = array(
				"plugin" => $row['plugin'],
				"id" => $row['pluginid'],
				"active" => ($row['active'] == 1) ? true : false );
		}
		
		$this->DESK->Database->Free($r);
	}
	
	/**
	 * Load PIMs (load and start activated PIMs)
	**/
	function LoadPIMS()
	{
		if ($this->installed_pims == null)
			$this->LoadInstalledPIMS();
		foreach($this->installed_pims as $plugin => $data)
		{
			if ($data['active'])
				$this->LoadPIM($plugin);
		}
	}
	
	/**
	 * Get a list of PIMS
	 * @return array list of PIMS
	**/
	function ListPIMS()
	{
		if ($this->installed_pims == null)
			$this->LoadInstalledPIMS();
		
		$out = array();
			
		$handle = opendir("plugins/");
		
		if ($handle !== false)
		{
			while(false !== ($file = readdir($handle)))
			{
				if ($file != "." && $file != ".." && is_dir("plugins/".$file))
				{
				
					if (file_exists("plugins/".$file."/".$file.".php"))
					{
						$out[$file] = array(
							"installed" => isset($this->installed_pims[$file]) ? true : false,
							"data" => isset($this->installed_pims[$file]) ? $this->installed_pims[$file] : array()
							);
					}
				}
			}
		
			closedir($handle);
		}
		return $out;
	}
	
	/**
	 * Install PIM
	 * @param string $plugin Plugin Name
	**/
	function InstallPIM($plugin)
	{
		$path = "plugins/".$plugin."/";
		$file = $path.$plugin.".php";
		include_once($file);
		
		$inst = new $plugin($this->DESK, $path, $path, 9999);
		
		$inst->Install();
		
		$q="INSERT INTO ".$this->DESK->Database->Table("plugins")."(".$this->DESK->Database->Field("plugin").",".$this->DESK->Database->Field("active").")";
		$q.=" VALUES(".$this->DESK->Database->SafeQuote($plugin).",".$this->DESK->Database->Safe("0").")";
		
		$this->DESK->Database->Query($q);
	}
	
	/**
	 * Set a PIM as active or not
	 * @param int $id ID (pluginid in database)
	 * @param bool $active Active flag
	**/
	function ActivatePIM($id, $active)
	{
	
		$q="SELECT * FROM ".$this->DESK->Database->Table("plugins")." WHERE ".$this->DESK->Database->Field("pluginid")."=".$this->DESK->Database->Safe($id);
		$r=$this->DESK->Database->Query($q);
		
		if ($row=$this->DESK->Database->FetchAssoc($r))
		{
			$plugin = $row['plugin'];
			$path = "plugins/".$plugin."/";
			$file = $path.$plugin.".php";
			include_once($file);
		
			$inst = new $plugin($this->DESK, $path, $path, 9999);
			
			if ($active)
				$inst->Activate();
			else
				$inst->Deactivate();
		
			$q="UPDATE ".$this->DESK->Database->Table("plugins")." SET ".$this->DESK->Database->Field("active")."=";
			if ($active)
				$q.="1";
			else
				$q.="0";
			$q.=" WHERE ".$this->DESK->Database->Field("pluginid")."=".$this->DESK->Database->Safe($id);
		
			$this->DESK->Database->Query($q);
		}
		
		$this->DESK->Database->Free($r);
	}
		
		
	/**
	 * Uninstall a PIM
	 * @param int $id ID (pluginid in database)
	**/
	function UninstallPIM($id)
	{
	
		$q="SELECT * FROM ".$this->DESK->Database->Table("plugins")." WHERE ".$this->DESK->Database->Field("pluginid")."=".$this->DESK->Database->Safe($id);
		$r=$this->DESK->Database->Query($q);
		
		if ($row=$this->DESK->Database->FetchAssoc($r))
		{
			$plugin = $row['plugin'];
			$path = "plugins/".$plugin."/";
			$file = $path.$plugin.".php";
			include_once($file);
		
			$inst = new $plugin($this->DESK, $path, $path, 9999);
			
			$inst->Uninstall();
		
			$q="DELETE FROM ".$this->DESK->Database->Table("plugins")." WHERE ".$this->DESK->Database->Field("pluginid")."=".$this->DESK->Database->Safe($id);
		
			$this->DESK->Database->Query($q);
		}
		
		$this->DESK->Database->Free($r);
	}
	
	/**
	 * Call BuildMenu on all PIMs
	**/
	function BuildMenu()
	{
		foreach($this->pims as $id => $pim)
			$pim->BuildMenu();
	}
	
}


?>
