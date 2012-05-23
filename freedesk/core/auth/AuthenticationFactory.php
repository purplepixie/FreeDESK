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
 * Authentication Factory - creates a concrete AuthMethod class as requested
**/
class AuthenticationFactory
{
	/**
	 * Create class as asked for or return a bool false
	 * @param mixed &$DESK Current FreeDESK instance
	 * @param string $method Method of Authentication
	 * @return mixed Class on success or bool false on failure
	**/
	static function Create(&$DESK, $method)
	{
		$methods = $DESK->PluginManager->GetType("Auth");
		
		foreach($methods as $pmethod)
		{
			if ($method == $pmethod->subtype)
			{
				if (class_exists($pmethod->classname))
				{
					return new $pmethod->classname($DESK);
				}
				else
					return false; // method found but illegal class
			}
		}
		return false; // no method found
	}
}
	
?>
