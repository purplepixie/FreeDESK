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
 * Skin class - controls skin elements as required
**/
class Skin
{
	/**
	 * FreeDESK instance
	**/
	private $DESK = null;
	
	/**
	 * Current Skin
	**/
	private $Skin = "default";
	
	/**
	 * Default/Fallback Skin
	**/
	private $Default = "default";
	
	/**
	 * Constructor
	 * @param mixed &$freeDESK FreeDESK instance pointer
	**/
	function Skin(&$freeDESK)
	{
		$this->DESK = &$freeDESK;
	}
	
	/**
	 * Set skin
	 * @param string Skin
	**/
	function SetSkin($skin)
	{
		$this->Skin = $skin;
	}
	
	/**
	 * Get a (physical) file location
	 * @param string $file Filename
	 * @return mixed Filepath (string) or bool false on failure to find
	**/
	function GetFileLocation($file)
	{
		$skinfile = $this->DESK->BaseDir . "skin/" . $this->Skin . "/" . $file;
		$deffile = $this->DESK->BaseDir . "skin/" . $this->Default . "/" . $file;
		if ($skinfile != $deffile) // seperate files to try
		{
			if (file_exists($skinfile))
				return $skinfile;
		}
		// Try the default
		if (file_exists($deffile))
			return $deffile;
		else
			return false; // no file found
	}
	
	/**
	 * Get a web location for a file
	 * @param string $file Filename
	 * @return mixed Filepath (string) or bool false on failure to find
	**/
	function GetWebLocation($file)
	{
		$webfile = "";
		$skinfile = $this->DESK->BaseDir . "skin/" . $this->Skin . "/" . $file;
		$deffile = $this->DESK->BaseDir . "skin/" . $this->Default . "/" . $file;
		if ($skinfile != $deffile) // seperate files to try
		{
			if (file_exists($skinfile))
				$webfile="skin/".$this->Skin."/".$file;
		}
		// Try the default
		if (($webfile=="")&&(file_exists($deffile)))
			$webfile="skin/".$this->Default."/".$file;
		else
			return false; // no file found
			
		return $webfile;
	}
	
	/**
	 * Include a file
	 * @param string $file Filename
	 * @param array $data Data element for skin element (optional)
	 * @return bool True on success or false on failure
	**/
	function IncludeFile($file, $data=array())
	{
		$filename=$this->GetFileLocation($file);
		if ($filename === false) return false; // file not found
		$DESK=&$this->DESK;
		
		include($filename);
	}
	
	/**
	 * Get a file contents
	 * @param string $file Filename
	 * @return mixed Contents on success or bool false on failure
	**/
	function GetFile($file)
	{
		$filename=$this->GetFileLocation($file);
		if ($filename === false) return false; // file not found
		
		return file_get_contents($filename);
	}
	
	/**
	 * Common Header Items Display
	**/
	function CommonHeader()
	{
		// FreeDESK Javascript
		echo "\n<script type=\"text/javascript\" src=\"js/freedesk.js\"></script>\n";
		// AJAX Javascript
		echo "<script type=\"text/javascript\" src=\"js/ajax.js\"></script>\n";
		// Alerts
		echo "<script type=\"text/javascript\" src=\"js/alerts.js\"></script>\n";
		// Search
		echo "<script type=\"text/javascript\" src=\"js/search.js\"></script>\n";
		/*
		echo "<script type=\"text/javascript\">\n";
		echo file_get_contents("js/freedesk.js");
		echo "\n";
		echo file_get_contents("js/ajax.js");
		echo "\n</script>\n";
		*/
		if ($this->DESK->ContextManager->IsOpen())
		{
			// Login-specific settings such as SID and request statuses
			echo "<script type=\"text/javascript\">\n";
			echo "DESK.sid = \"".$this->DESK->ContextManager->Session->sid."\";\n";
			
			$statuses = $this->DESK->RequestManager->StatusList();
			foreach($statuses as $key => $val)
			{
				echo "DESK.requestStatus[".$key."]=\"".$val."\";\n";
			}
			
			// Request list display fields
			$fields = $this->DESK->RequestManager->FetchFields();
			$a=0;
			foreach($fields as $key => $val)
			{
				echo "DESK.fieldList[".$a++."] = [ \"".$val[0]."\" , ".$val[1]." , \"".$key."\" ];\n";
			}
			
			echo "</script>\n";
		}
		
		
	}
	
	/**
	 * Common Footer Items (pre end of HTML) display
	**/
	function CommonFooter()
	{
		echo "\n";
		echo "<form id=\"login_sid_form\" action=\"./\" method=\"post\">\n";
		echo "<input type=\"hidden\" name=\"sid\" value=\"\">\n";
		echo "</form>\n";
	}
	
	/**
	 * Common body items (just after the body tag) display
	**/
	function CommonBody()
	{
		echo "\n";
		$this->IncludeFile("login.php");
		echo "<div id=\"screen_backdrop\" class=\"screen_backdrop\"></div>\n";
		echo "<div id=\"loading\" class=\"loading\"></div>\n";
	}
	
}
?>
	
	
	
