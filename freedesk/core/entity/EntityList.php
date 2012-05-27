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
 * Entity List Iterator - iterate through the contents of an EntityList
**/
class EntityListIterator
{
	/**
	 * Current list
	**/
	private $list = null;
	
	/**
	 * Current/last index
	**/
	private $index = -1;
	
	/**
	 * Constructor
	 * @param $list The Entity List to iterate through
	**/
	function EntityListIterator(&$list)
	{
		$this->list = &$list;
		$this->index = -1;
	}
	
	/**
	 * Fetch next
	 * @return mixed Entity object or bool null on failure
	**/
	function FetchNext()
	{
		return $this->list->Fetch[++$this->index];
	}
}

/**
 * Entity List - list of Entity objects found e.g. in a search
**/
class EntityList
{
	/**
	 * Data Items
	**/
	private $items = array();
	
	/**
	 * Count of Items
	**/
	private $count = 0;
	
	/**
	 * Add an item
	 * @param mixed $item Entity object to add to list
	**/
	function Add(&$item)
	{
		$this->items[]=&$item;
		$this->count++;
	}
	
	/**
	 * Fetch an item by index
	 * @param int $item Item Index
	 * @return mixed Entity object if found or bool null if not found
	**/
	function Fetch($item)
	{
		if (isset($this->items[$item]))
			return $this->items[$item];
		else
			return false;
	}
}

?>

