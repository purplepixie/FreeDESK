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
 * Request Manager - handle all management of requests
**/
class RequestManager
{
	/**
	 * FreeDESK instance
	**/
	private $DESK = null;
	
	/**
	 * Team List
	**/
	private $Teams = null;
	
	/**
	 * User List (Assignment)
	**/
	private $Users = null;
	
	/**
	 * Constructor
	 * @param mixed &$freeDESK FreeDESK instance
	**/
	function RequestManager(&$freeDESK)
	{
		$this->DESK=&$freeDESK;
		$this->DESK->PluginManager->Register(new Plugin(
			"Request Manager", "0.01", "Core" ));
		// Register our permissions
		$this->DESK->PermissionManager->Register("request.view.otherteam",false);
		$this->DESK->PermissionManager->Register("request.view.otherteamuser",false);
		$this->DESK->PermissionManager->Register("request.view.otheruser",false);
		$this->DESK->PermissionManager->Register("request.view.unassigned",false);
		$this->DESK->PermissionManager->Register("request.assign.otherteam",false);
		$this->DESK->PermissionManager->Register("request.assign.otherteamuser",false);
		$this->DESK->PermissionManager->Register("request.assign.otheruser",false);
		$this->DESK->PermissionManager->Register("request.assign.unassigned",false);
		
	}
	
	/**
	 * Team and User List (Assignment and View List)
	 * @return array Mixed array with teams and users with view and assign flags
	**/
	function TeamUserList()
	{
		$out = array();
		
		$this->Users = array();
		$this->Teams = array();
		
		$q="SELECT ".$this->DESK->Database->Field("username").",".$this->DESK->Database->Field("realname")." FROM ".$this->DESK->Database->Table("user");
		$r=$this->DESK->Database->Query($q);
		$users=array();
		while($row=$this->DESK->Database->FetchAssoc($r))
		{
			$users[$row['username']] = $row['realname'];
			$this->Users[$row['username']] = $row['realname'];
		}
		$this->DESK->Database->Free($r);

		$q="SELECT * FROM ".$this->DESK->Database->Table("team");
		$r=$this->DESK->Database->Query($q);
		$team=array();
		while($row=$this->DESK->Database->FetchAssoc($r))
		{
			$team[$row['teamid']]=$row['teamname'];
			$this->Teams[$row['teamid']]=$row['teamname'];
		}

		$q="SELECT * FROM ".$this->DESK->Database->Table("teamuserlink");
		$r=$this->DESK->Database->Query($q);
		$teamlink=array();
		while($row=$this->DESK->Database->FetchAssoc($r))
		{
			if (isset($teamlink[$row['teamid']]))
				$teamlink[$row['teamid']][]=$row['username'];
			else
				$teamlink[$row['teamid']]=array( $row['username'] );
		}

		$out[0]=array(
			"name" => "Unassigned",
			"id" => 0,
			"team" => true,
			"assign" => true,
			"view" => true,
			"items" => array() );

		foreach($team as $teamid => $teamname)
		{
			$out[$teamid]=array(
				"name" => $teamname,
				"id" => $teamid,
				"team" => true,
				"assign" => true,
				"view" => true,
				"items" => array() );
				
			if (isset($teamlink[$teamid]))
			{
					
				foreach($teamlink[$teamid] as $username)
				{
					$out[$teamid]["items"][$username] = array (
						"username" => $username,
						"realname" => $users[$username],
						"assign" => true,
						"view" => true );
				}
			}
		}

		$out['allusers']=array(
			"name" => "All Users",
			"id" => 0,
			"team" => false,
			"assign" => false,
			"view" => false,
			"items" => array() );
		foreach($users as $username => $realname)
		{
			$out['allusers']['items'][$username] = array(
				"username" => $username,
				"realname" => $realname,
				"assign" => true,
				"view" => true );
		}
		
		return $out;
	}
	
	/**
	 * Return a list of possible request statuses
	 * @return array Status list
	**/
	function StatusList()
	{
		$q="SELECT * FROM ".$this->DESK->Database->Table("status");
		$r=$this->DESK->Database->Query($q);
		$out=array();
		while ($row=$this->DESK->Database->FetchAssoc($r))
		{
			$out[$row['status']]=$row['description'];
		}
		$this->DESK->Database->Free($r);
		return $out;
	}
	
	/**
	 * Fetch a request by ID
	 * @param int $request Request ID
	 * @return mixed bool false if request not found or Request-type class on success
	**/
	function Fetch($request)
	{
		$q="SELECT * FROM ".$this->DESK->Database->Table("request")." WHERE ";
		$q.=$this->DESK->Database->Field("requestid")."=".$this->DESK->Database->Safe($request);
		$r=$this->DESK->Database->Query($q);
		if ($row=$this->DESK->Database->FetchAssoc($r))
		{
			// TODO: Request types and classes
			$req = new Request($this->DESK);
			foreach($row as $key => $val)
				$req->Set($key, $val);
			$assign="";
			if ($row['assignteam']!=0)
			{
				$teams = $this->TeamList();
				$assign.=$teams[$row['assignteam']];
			}
			if ($row['assignuser']!="")
			{
				if ($assign!="")
					$assign.=" - ";
				$users = $this->UserList();
				$assign.=$users[$row['assignuser']];
			}
			$req->Set("assigned",$assign);
			return $req;
		}
		else
			return false;
	}
	
	/**
	 * Fetch a request assignment list
	 * @param int $teamid Assigned team (optional, default 0)
	 * @param string $username Assigned username (optional, default "")
	 * @param string $sort Field to sort on
	 * @param string $order Order (ASC or DESC)
	 * @return mixed array of requests matching
	**/
	function FetchAssigned($teamid=0, $username="", $sort="", $order="")
	{
		// assignteam assignuser
		$q="SELECT ".$this->DESK->Database->Field("requestid")." FROM ".$this->DESK->Database->Table("request")." WHERE ";
		
		
		if ( ($teamid==0) && ($username!="") ) // assigned to a user for any team
			$q.=$this->DESK->Database->Field("assignuser")."=".$this->DESK->Database->SafeQuote($username);
		else // use both
		{
			$q.=$this->DESK->Database->Field("assignuser")."=".$this->DESK->Database->SafeQuote($username)." AND ";
			$q.=$this->DESK->Database->Field("assignteam")."=".$this->DESK->Database->Safe($teamid);
		}
		
		if ($sort != "")
		{
			$q.=" ORDER BY ".$this->DESK->Database->Field($sort)." ";
			if ($order == "ASC")
				$q.="ASC";
			else
				$q.="DESC";
		}
		
		$out=array();
		$r=$this->DESK->Database->Query($q);
		while ($row=$this->DESK->Database->FetchAssoc($r))
		{
			$out[]=$this->Fetch($row['requestid']);
		}
		return $out;
	}
	
	/**
	 * Fetch an array of request fields for the main list display with their default display options
	 * @return array of request fields
	**/
	function FetchFields()
	{
		$out = array(
			"requestid" => array("Request ID", 1),
			"customer" => array("Customer", 1),
			"assigned" => array("Assigned To", 1),
			"openeddt" => array("Opened", 0),
			"class" => array("Class", 0),
			"status" => array("Status", 1) );
		return $out;
	}
	
	/**
	 * Fetch a list of users in form username=>realname
	 * @return array User list
	**/
	function UserList()
	{
		if (!is_array($this->Users))
			$this->TeamUserList();
		return $this->Users;
	}
	
	/**
	 * Fetch a list of teams in form teamid=>teamname
	 * @return array Team List
	**/
	function TeamList()
	{
		if (!is_array($this->Teams))
			$this->TeamUserList();
		return $this->Teams;
	}
}
?>
