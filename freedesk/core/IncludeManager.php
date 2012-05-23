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
 * Include class handles most php source file includes at runtime
**/

class IncludeManager
{
	/**
	 * Reference to FreeDESK class
	**/
	private $DESK = null;
	/**
	 * Base directory
	**/
	private $baseDir = "./";
	
	/**
	 * Constructor
	 * @param object $freeDESK freedesk main object
	 * @param string $baseDir base directory
	**/
	function IncludeManager(&$freeDESK, $baseDir)
	{
		$this->DESK = &$freeDESK;
		$this->baseDir = $baseDir;
	}

	/**
	 * Include a File
	 * @param string $filepath Relative filepath from base dir
	 * @param bool $required Is opened using require() rather than include() (default false)
	 * @param bool $once Is opened using include_once() (default false)
	**/
	function IncludeFile($filepath, $required=false, $once=false)
	{
		if ($required)
		{
			require($this->baseDir.$filepath);
				//or die("Failed to open required file ".$filepath);
		}
		else if ($once)
		{
			include_once($this->baseDir.$filepath);
		}
		else
		{
			include($this->baseDir.$filepath);
		}
	}
	
	/**
	 * Include a file and create a class instance, uses require to open file
	 * @param string $filepath Path to file
	 * @param string $classname Name of the class to create
	 * @param bool $passdesk Pass the FreeDESK object to constructor (optional, default true)
	 * @return object Newly created class
	**/
	function IncludeInstance($filepath, $classname, $passdesk=true)
	{
		$this->IncludeFile($filepath,true);
		$c = null;
		if ($passdesk)
			$c=new $classname($this->DESK);
		else
			$c=new $classname();
		return $c;
	}
	
	/**
	 * Include a file and execute a static method in that class, uses require to open
	 * @param string $filepath Path to file
	 * @param string $classname Name of class
	 * @param string $methodname Name of method (optional, default Exec)
	 * @param bool $passdesk Pass the FreeDESK object to the method (optional, default true)
	**/
	function IncludeExec($filepath, $classname, $methodname="Exec", $passdesk=true)
	{
		$this->IncludeFile($filepath,true);
		if ($passdesk)
			$classname::$methodname($this->DESK);
		else
			$classname::$methodname();
	}

}







?>
