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
 * Entity Factory - create an entity of the correct class
**/
class EntityFactory
{
	/**
	 * Register ourselves with FreeDESK
	 * @param mixed $desk FreeDESK instance
	**/
	static function Exec(&$desk)
	{
		$desk->PluginManager->Register(new Plugin(
			"Entity Factory", "0.01", "Core", "" ));
	}
	
	/**
	 * Create an instance of the required entity class
	 * @param mixed &$desk FreeDESK instance
	 * @param string $entity Entity
	 * @return mixed Entity object
	**/
	static function Create(&$desk, $entity)
	{
		$methodlist = $desk->PluginManager->GetType("Entity");
		$table = $desk->DataDictionary->GetTable($entity);
		
		if ($table)
			$class = $table->class;
		else
			$class = "";
		
		$default = "Entity"; // fail-safe
		foreach($methodlist as $method)
		{
			if ($method->subtype == $class)
				return new $method->classname($desk);
			if ($method->subtype == "")
				$default=$method->classname;
		}
		return new $default($desk);
	}
	
}
?>
