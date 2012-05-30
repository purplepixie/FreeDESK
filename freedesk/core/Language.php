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
 * FreeDESK Language Class
**/
class Language
{
	/**
	 * FreeDESK Instance
	**/
	private $DESK = null;
	
	/**
	 * Default Language
	**/
	private $default = "English";
	
	/**
	 * Language Items
	**/
	private $items = array();
	
	/**
	 * Constructor
	 * @param mixed &$freeDESK FreeDESK instance
	**/
	function Language(&$freeDESK)
	{
		$this->DESK = &$freeDESK;
		$this->Load($this->default, true);
	}
	
	/**
	 * Load Language
	 * @param string $language
	 * @param bool $override Override already-loaded check (optional, default false)
	 * @return bool True on success or false on failure
	**/
	function Load($language, $override=false)
	{
		if (!$override && ($language == $this->default))
			return true; // already loaded
		$class="FDL_".$language;
		$this->DESK->Include->IncludeFile("language/".$language.".php", false, true);
		if (class_exists($class))
			$class::$language($this->items);
		else
			return false;
		return true;
	}
	
	/**
	 * Get a language element
	 * @param string $element Language element
	**/
	function Get($element)
	{
		if (isset($this->items[$element]))
			return $this->items[$element];
		else
			return "ULE:".$element;
	}
	
}
?>
