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
 * AuthMethodStandard is the standard (database-based) authentication method
**/
class AuthMethodStandard extends AuthMethodBase
{
	/**
	 * AuthMethodStandard Constructor
	 * @param mixed $freeDESK FreeDESK instance
	**/
	function AuthMethodStandard(&$freeDESK)
	{
		parent::AuthMethodBase($freeDESK);
	}
	
	/**
	 * Authenticate a user/customer session
	 * @param int $type Type of Context (ContextType)
	 * @param string $username Username provided
	 * @param string $password Password provided
	 * @return bool True on success or false on failure
	**/
	function Authenticate($type, $username, $password)
	{
		$valid=false; // default to failed
	
		if ($type == ContextType::User)
		{
			$password=$this->DESK->BaseConfig->pwd_Hash.$password; // add the hash to the password
			$q="SELECT * FROM ".$this->DESK->Database->Table("user")." WHERE ";
			$q.=$this->DESK->Database->Field("username")."=\"".$this->DESK->Database->Safe($username)."\" AND ";
			$q.=$this->DESK->Database->Field("password")."=MD5(\"".$this->DESK->Database->Safe($password)."\") ";
			$q.="LIMIT 0,1";
			
			$r=$this->DESK->Database->Query($q);
			
			if ($user = $this->DESK->Database->FetchAssoc($r))
				$valid=true;
			
			$this->DESK->Database->Free($r);
		}
		
		// TODO: Customer Authentication
		
		return $valid;
	}
	
	/**
	 * Set a standard user password - note no security here and that this is NOT a AutoMethodBase overrided function
	 * @param string $username Username
	 * @param string $password Password
	**/
	function SetPassword($username, $password)
	{
		$password=$this->DESK->BaseConfig->pwd_Hash.$password;
		$q="UPDATE ".$this->DESK->Database->Table("user")." SET ";
		$q.=$this->DESK->Database->Field("password")."=MD5(\"".$this->DESK->Database->Safe($password)."\") ";
		$q.="WHERE ".$this->DESK->Database->Field("username")."=\"".$this->DESK->Database->Safe($username)."\"";
		$this->DESK->Database->Query($q);
	}
	
	/**
	 * Exec function (static)
	 * @param mixed $DESK Current FreeDESK instance
	**/
	static function Exec(&$DESK)
	{
		$plugin = new Plugin();
		$plugin->name="Standard Authentication";
		$plugin->version="0.01";
		$plugin->type="Auth";
		$plugin->subtype="standard";
		$plugin->classname="AuthMethodStandard";
		$DESK->PluginManager->Register($plugin);
	}
}
?>
