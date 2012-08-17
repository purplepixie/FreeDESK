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
 * AuthMethodBase is the abstract base class for authentication methods
**/
abstract class AuthMethodBase extends FreeDESK_PIM
{
	/**
	 * AuthMethodBase Constructor
	 * @param mixed $freeDESK FreeDESK instance
	**/
	function AuthMethodBase(&$freeDESK)
	{
		parent::FreeDESK_PIM(&$freeDESK, "", "", 0);
	}
	
	/**
	 * Authenticate a user/customer session
	 * @param int $type Type of Context (ContextType)
	 * @param string $username Username provided
	 * @param string $password Password provided
	 * @return bool True on success or false on failure
	**/
	abstract function Authenticate($type, $username, $password);
}
?>
