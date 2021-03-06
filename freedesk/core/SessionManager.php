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
 * Session Class - contains information about interactive user session
**/
class Session
{
	/**
	 * Type of session
	**/
	var $type = ContextType::None;
	/**
	 * Session ID
	**/
	var $sid = "";
	/**
	 * Username
	**/
	var $username = "";
	/**
	 * Real name
	**/
	var $realname = "";
	/**
	 * Create a SID - sets $this->sid and returns SID
	 * @return string SID
	**/
	function CreateSID()
	{
		$allow = "abcdefghijklmnopqrstuvwxyz0123456789XYZ";
		$len = 128;
		$allowlen = strlen($allow);
		$this->sid="";
		mt_srand(microtime()*1000000);
		for ($i=0; $i<$len; ++$i)
		{
			$this->sid.=$allow[mt_rand(0,$allowlen-1)];
		}
		return $this->sid;
	}
	/**
	 * Return XML Description of the Session
	 * @param bool $header Return XML header (optional, default false)
	 * @return string XML Data
	**/
	function XML($header=false)
	{
		$xml = new xmlCreate();
		$xml->startElement("session");
		$xml->charElement("type",$this->type);
		$xml->charElement("sid",$this->sid);
		$xml->charElement("username",$this->username);
		$xml->endElement("session");
		return $xml->getXML($header);
	}
	/**
	 * Get a 'nice' name
	 * @return string Nice name for current user
	**/
	function NiceName()
	{
		$name = "";
		if ($this->type == ContextType::Customer)
			$name.="CUSTOMER";
		else if ($this->realname != "")
			$name.=$this->realname;
		else
			$name.=$this->username;
		return $name;
	}
}

/**
 * Session Manager class - handles creation, check and update of sessions
**/
class SessionManager
{
	/**
	 * FreeDESK Instance
	**/
	private $DESK = null;
	
	/**
	 * Constructor
	 * @param mixed $freeDESK FreeDESK instance
	**/
	function SessionManager(&$freeDESK)
	{
		$this->DESK = &$freeDESK;
		$this->DESK->PluginManager->Register(new Plugin(
			"Session Manager", "0.01", "Core" ));
	}
	
	/**
	 * Create a Session
	 * @param mixed $type Type of session of form ContextType)
	 * @param string $username Username
	 * @param string $password Password
	 * @return mixed Session class on success or bool false on failure
	**/
	function Create($type, $username, $password)
	{	// TODO: Customer
		$expiry = $this->DESK->Configuration->Get("session.expire","15");
		
		if ($type == ContextType::User)
		{
		
			// Fetch user auth type
		
			$q="SELECT ".$this->DESK->Database->Field("authtype").",".$this->DESK->Database->Field("realname")." FROM ".$this->DESK->Database->Table("user")." ";
			$q.="WHERE ".$this->DESK->Database->Field("username")."=\"".$this->DESK->Database->Safe($username)."\" LIMIT 0,1";
		
			
			$r=$this->DESK->Database->Query($q);
			$user=$this->DESK->Database->FetchAssoc($r);
			$this->DESK->Database->Free($r);
			if ($user)
			{
				$authtype=$user['authtype'];
				if ($authtype=="")
					$authtype=$this->DESK->Configuration->Get("auth.default","standard");
				$authmethod=AuthenticationFactory::Create($this->DESK, $authtype);
				if (!$authmethod)
					return false;
				if ($authmethod->Authenticate($type, $username, $password))
				{
					// Successful Login
					$session = new Session();
					$session->type = $type;
					$session->username = $username;
					$session->realname = $user['realname'];
					$session->CreateSID();
				
					// Create the session in the DB
					$q="INSERT INTO ".$this->DESK->Database->Table("session")."(".$this->DESK->Database->Field("username").",";
					$q.=$this->DESK->Database->Field("session_id").",".$this->DESK->Database->Field("sessiontype").",";
					$q.=$this->DESK->Database->Field("created_dt").",".$this->DESK->Database->Field("updated_dt").",";
					$q.=$this->DESK->Database->Field("expires_dt").",".$this->DESK->Database->Field("realname").") VALUES(";
					$q.="\"".$this->DESK->Database->Safe($username)."\",";
					$q.="\"".$this->DESK->Database->Safe($session->sid)."\",";
					$q.=$this->DESK->Database->Safe($type).",";
					$q.="NOW(),NOW(),DATE_ADD(NOW(), INTERVAL ".$this->DESK->Database->Safe($expiry)." MINUTE),";
					$q.=$this->DESK->Database->SafeQuote($user['realname']).")";
				
					$this->DESK->Database->Query($q);
				
					return $session;
				}
			}
			return false; // failure
		}
		else if ($type == ContextType::Customer && $username!="" && $password!="")
		{
			$q="SELECT * FROM ".$this->DESK->Database->Table("customer")." WHERE ";
			$q.="(".$this->DESK->Database->Field("username")."=".$this->DESK->Database->SafeQuote($username)." OR ";
			$q.=$this->DESK->Database->Field("email")."=".$this->DESK->Database->SafeQuote($username).") AND ";
			$q.=$this->DESK->Database->Field("password")."=".$this->DESK->Database->SafeQuote($password);
			$q.=" LIMIT 0,1";
			
			$r=$this->DESK->Database->Query($q);
			if ($user=$this->DESK->Database->FetchAssoc($r))
			{
				$this->DESK->Database->Free($r);
				// Successful Login
				$session = new Session();
				$session->type = $type;
				$session->username = $user['customerid'];
				$session->realname = "CUSTOMER:".$user['firstname']." ".$user['lastname'];
				$session->CreateSID();
			
				// Create the session in the DB
				$q="INSERT INTO ".$this->DESK->Database->Table("session")."(".$this->DESK->Database->Field("username").",";
				$q.=$this->DESK->Database->Field("session_id").",".$this->DESK->Database->Field("sessiontype").",";
				$q.=$this->DESK->Database->Field("created_dt").",".$this->DESK->Database->Field("updated_dt").",";
				$q.=$this->DESK->Database->Field("expires_dt").",".$this->DESK->Database->Field("realname").") VALUES(";
				$q.="\"".$this->DESK->Database->Safe($session->username)."\",";
				$q.="\"".$this->DESK->Database->Safe($session->sid)."\",";
				$q.=$this->DESK->Database->Safe($type).",";
				$q.="NOW(),NOW(),DATE_ADD(NOW(), INTERVAL ".$this->DESK->Database->Safe($expiry)." MINUTE),";
				$q.=$this->DESK->Database->SafeQuote($user['realname']).")";
			
				$this->DESK->Database->Query($q);
			
				return $session;
			}
			else
				return false; // failed login
		}
		return false; // default failure
	}
	
	/**
	 * Check a Session
	 * @param mixed $sid Session ID
	 * @return mixed Sesson class on success or bool false on failure
	**/
	function Check($sid)
	{
		$expiry = $this->DESK->Configuration->Get("session.expire","15");
		
		// Select session from DB
		$q="SELECT * FROM ".$this->DESK->Database->Table("session")." WHERE ".$this->DESK->Database->Field("session_id")."=";
		$q.="\"".$this->DESK->Database->Safe($sid)."\" AND ".$this->DESK->Database->Field("expires_dt").">NOW() LIMIT 0,1";
		
		$r=$this->DESK->Database->Query($q);
		$sess=$this->DESK->Database->FetchAssoc($r);
		$this->DESK->Database->Free($r);
		if ($sess) // If session found
		{
			// Load session data
			$session = new Session();
			$session->sid = $sid;
			$session->type = $sess['sessiontype'];
			$session->username = $sess['username'];
			$session->realname = $sess['realname'];
			
			// And update expiry
			$q="UPDATE ".$this->DESK->Database->Table("session")." SET ".$this->DESK->Database->Field("updated_dt")."=NOW(),";
			$q.=$this->DESK->Database->Field("expires_dt")."=DATE_ADD(NOW(), INTERVAL ".$this->DESK->Database->Safe($expiry)." MINUTE) ";
			$q.="WHERE ".$this->DESK->Database->Field("session_id")."=\"".$this->DESK->Database->Safe($sid)."\"";
			$this->DESK->Database->Query($q);
			
			return $session;
		}
		return false;
	}
	
	/**
	 * Destroy a session
	 * @param string $sid Session ID
	**/
	function Destroy($sid)
	{
		$q="DELETE FROM ".$this->DESK->Database->Table("session")." WHERE ".$this->DESK->Database->Field("session_id")."=";
		$q.=$this->DESK->Database->SafeQuote($sid);
		$this->DESK->Database->Query($q);
	}
}


?>

