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
 * Context Connection Types
**/
abstract class ContextType
{
	/**
	 * None
	**/
	const None = -1;
	/**
	 * System
	**/
	const System = 0;
	/**
	 * User
	**/
	const User = 1;
	/**
	 * Customer
	**/
	const Customer = 2;
}

/**
 * Context Manager for FreeDESK - handle connections to the system
**/
class ContextManager
{
	/**
	 * FreeDESK instance
	**/
	private $DESK = null;
	
	/**
	 * Open Context Flag
	**/
	private $open = false;
	
	/**
	 * Context Type
	**/
	private $type = ContextType::None;
	
	/**
	 * Current Session
	**/
	var $Session = null;
	
	
	/**
	 * Session Manager
	**/
	private $SessionManager = null;
	
	/**
	 * Constructor
	 * @param mixed $freeDESK FreeDESK instance
	**/
	function ContextManager(&$freeDESK)
	{
		$this->DESK = &$freeDESK;
		$this->DESK->PluginManager->Register(new Plugin(
			"Context Manager", "0.01", "Core" ));
		// Load the SessionManager
		$this->SessionManager = $this->DESK->Include->IncludeInstance("core/SessionManager.php","SessionManager");
	}
	
	/**
	 * Check if context is open
	 * @return bool open flag
	**/
	function IsOpen()
	{
		return $this->open;
	}
	
	/**
	 * Get the current type of open context
	**/
	function GetType()
	{
		if (!$this->open)
			return ContextType::None;	// should be that if closed but to be safe
		return $this->type;
	}
	
	/**
	 * Check if has permission
	 * @param string $perm Permission type requested
	 * @return bool flag indicating if context has permission
	**/
	function Permission($perm)
	{
		if (!$this->open)
			return false; // no open context
		else if ($this->type == ContextType::None)
			return false; // no context type
		else if ($this->type == ContextType::System)
			return true; // system context - all permissions
		else if ($this->type == ContextType::User)
		{
			return $this->DESK->PermissionManager->UserPermission($perm, $this->Session->username);
		}
		else if ($this->type == ContextType::Customer)
			return true; // TO BE IMPLEMENTED WITH PermissionManager
		else
			return false; // default - refused
	}
	
	/**
	 * Open a context
	 * @param mixed $type Context type (from ContextType consts)
	 * @param string $sid Session ID if existing session (optional)
	 * @param string $username Username (optional)
	 * @param string $password Password (optional)
	 * @return bool returns true if successful or false on failure
	**/
	function Open($type, $sid="", $username="", $password="")
	{
		if ($type == ContextType::System)
		{
			$this->type = $type;
			$this->open = true;
			return true;
		}
		else if ($type == ContextType::User)
		{
			if ($sid=="")
			{
				$session=$this->SessionManager->Create($type, $username, $password);
				if (!$session) // session creation failed
				{
					$this->DESK->LoggingEngine->Log("Session Creation Failed for User ".$username, "Context", "Fail", 4);
					return false;
				}
				else
				{
					$this->DESK->LoggingEngine->Log("Session Created for User ".$username, "Context", "Open", 9);
					$this->type = $type;
					$this->open = true;
					$this->Session=$session;
					return true;
				}
			}
			else // pre-existing session
			{
				$session=$this->SessionManager->Check($sid);
				if (!$session)
				{
					$this->DESK->LoggingEngine->Log("Session Check Failed for SID ".$sid, "Context", "Fail", 9);
					return false;
				}
				else
				{
					$this->DESK->LoggingEngine->Log("Session Check for User ".$session->username, "Context", "Check", 9);
					$this->type = $type;
					$this->open = true;
					$this->Session=$session;
					return true;
				}
			}
		}
		else if ($type == ContextType::Customer)
		{
			//
		}
		return false;
	}
	
	/**
	 * Close the open context
	**/
	function Close()
	{
		$this->Session = null;
		$this->type = ContextType::None;
		$this->open = false;
	}
	
	
}
?>
