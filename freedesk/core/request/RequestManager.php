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
	 * Request classes list
	**/
	private $ClassList = null;
	
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
		$this->DESK->PermissionManager->Register("request_view_otherteam",false);
		$this->DESK->PermissionManager->Register("request_view_otherteamuser",false);
		$this->DESK->PermissionManager->Register("request_view_otheruser",false);
		$this->DESK->PermissionManager->Register("request_view_unassigned",false);
		$this->DESK->PermissionManager->Register("request_assign_otherteam",false);
		$this->DESK->PermissionManager->Register("request_assign_otherteamuser",false);
		$this->DESK->PermissionManager->Register("request_assign_otheruser",false);
		$this->DESK->PermissionManager->Register("request_assign_unassigned",false);
		
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
		$q.=" ORDER BY ".$this->DESK->Database->Field("status")." DESC";
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
			$req = $this->CreateByID($row['class']);
			$req->ID = $row['requestid'];
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
			if ($row['assignteam']==0 && $row['assignuser']=="")
				$assign=$this->DESK->Lang->Get("unassigned");
			$req->Set("assigned",$assign);
			
			$cq="SELECT ".$this->DESK->Database->Field("firstname").",".$this->DESK->Database->Field("lastname");
			$cq.=" FROM ".$this->DESK->Database->Table("customer")." ";
			$cq.="WHERE ".$this->DESK->Database->Field("customerid")."=".$this->DESK->Database->Safe($row['customer']);
			$cq.=" LIMIT 0,1";
			$cr=$this->DESK->Database->Query($cq);
			$req->Set("customerid",$row['customer']);
			if ($cust=$this->DESK->Database->FetchAssoc($cr))
			{
				$req->Set("customer",$cust['firstname']." ".$cust['lastname']);
			}
			else
				$req->Set("customer","Unknown (".$row['customer'].")");
			$this->DESK->Database->Free($cr);
			
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
		
		$q.=" AND ".$this->DESK->Database->Field("status").">0";
		
		if ($sort != "" && $sort != "assigned" && $sort != "customer")
		{
			$q.=" ORDER BY ".$this->DESK->Database->Field($sort)." ";
			if ($order == "ASC")
				$q.="ASC";
			else
				$q.="DESC";
		}
		else if ($sort == "assigned")
		{
			if ($order == "ASC")
				$o="ASC";
			else
				$o="DESC";
			$q.=" ORDER BY ".$this->DESK->Database->Field("assignteam")." ".$o.",";
			$q.=$this->DESK->Database->Field("assignuser")." ".$o;
		}
		else if ($sort == "customer")
		{
			if ($order == "ASC")
				$o="ASC";
			else
				$o="DESC";
			$q.=" ORDER BY ".$this->DESK->Database->Field("customer")." ".$o;
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
			"priority" => array("Priority", 1),
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
	
	/**
	 * Determine is a user is in a team
	 * @param string $username Username
	 * @param int $teamid Team ID
	 * @return bool True if user is in team else false
	**/
	function IsUserInTeam($username, $teamid)
	{
		$q="SELECT ".$this->DESK->Database->Field("linkid")." FROM ";
		$q.=$this->DESK->Database->Table("teamuserlink")." WHERE ";
		$q.=$this->DESK->Database->Field("teamid")."=".$this->DESK->Database->Safe($teamid)." AND ";
		$q.=$this->DESK->Database->Field("username")."=".$this->DESK->Database->SafeQuote($username);
		$q.=" LIMIT 0,1";
		
		$r=$DESK->Database->Query($q);
		
		$inteam=false;
		
		if ($row=$DESK->Database->FetchAssoc($r))
			$inteam=true;
			
		$DESK->Database->Free($r);
		
		return $inteam;
	}
	
	/**
	 * Load a class list
	**/
	private function LoadClassList()
	{
		$q="SELECT * FROM ".$this->DESK->Database->Table("requestclass");
		$r=$this->DESK->Database->Query($q);
		$this->ClassList = array();
		while ($row=$this->DESK->Database->FetchAssoc($r))
		{
			$this->ClassList[$row['classid']] = $row;
		}
		$this->DESK->Database->Free($r);
	}
	
	/**
	 * Create a request by classid
	 * @param int $classid Class ID
	 * @return object Request object
	**/
	function CreateByID($classid)
	{
		if ($this->ClassList == null)
			$this->LoadClassList();
		
		if (isset($this->ClassList[$classid]))
			return RequestFactory::Create($this->DESK, $this->ClassList[$classid]['classclass']);
		else
			return RequestFactory::Create($this->DESK, "");
	}
	
	/**
	 * Create a new team
	 * @param string $teamname Name of the team
	**/
	function CreateTeam($teamname)
	{
		$q="INSERT INTO ".$this->DESK->Database->Table("team")."(".$this->DESK->Database->Field("teamname").") VALUES(".
		$q.=$this->DESK->Database->SafeQuote($teamname).")";
		$this->DESK->Database->Query($q);
	}
	
	/**
	 * Update a team name
	 * @param int $teamid ID
	 * @param string $teamname Team name
	**/
	function UpdateTeam($teamid, $teamname)
	{
		$q="UPDATE ".$this->DESK->Database->Table("team")." SET ".$this->DESK->Database->Field("teamname")."=".$this->DESK->Database->SafeQuote($teamname);
		$q.=" WHERE ".$this->DESK->Database->Field("teamid")."=".$this->DESK->Database->Safe($teamid);
		$this->DESK->Database->Query($q);
	}
	
	/**
	 * Delete a team
	 * @param int $teamid ID
	**/
	function DeleteTeam($teamid)
	{
		$q="DELETE FROM ".$this->DESK->Database->Table("teamuserlink")." WHERE ".$this->DESK->Database->Field("teamid")."=".$this->DESK->Database->Safe($teamid);
		$this->DESK->Database->Query($q);
		
		$q="DELETE FROM ".$this->DESK->Database->Table("team")." WHERE ".$this->DESK->Database->Field("teamid")."=".$this->DESK->Database->Safe($teamid);
		$this->DESK->Database->Query($q);
	}
	
	/**
	 * Create a new status
	 * @param string $name Status name
	**/
	function CreateStatus($name)
	{
		$current = $this->StatusList();
		$high = 0;
		foreach($current as $id => $curname)
			if ($id > $high)
				$high = $id;
		$newid = $high+1;
		
		$q="INSERT INTO ".$this->DESK->Database->Table("status")."(".$this->DESK->Database->Field("status").",".$this->DESK->Database->Field("description").") ";
		$q.="VALUES(".$this->DESK->Database->Safe($newid).",".$this->DESK->Database->SafeQuote($name).")";
		
		$this->DESK->Database->Query($q);
	}
	
	/**
	 * Update a status description
	 * @param int $id Status ID
	 * @param string $name New Name
	**/
	function UpdateStatus($id, $name)
	{
		$q="UPDATE ".$this->DESK->Database->Table("status")." SET ".$this->DESK->Database->Field("description")."=".$this->DESK->Database->SafeQuote($name);
		$q.=" WHERE ".$this->DESK->Database->Field("status")."=".$this->DESK->Database->Safe($id);
		$this->DESK->Database->Query($q);
	}
	
	/**
	 * Delete a status
	 * @param int $id Status ID
	**/
	function DeleteStatus($id)
	{
		$q="DELETE FROM ".$this->DESK->Database->Table("status")." WHERE ".$this->DESK->Database->Field("status")."=".$this->DESK->Database->Safe($id);
		$this->DESK->Database->Query($q);
	}
	
	/**
	 * Get a list of all request classes
	 * @return array Request class data
	**/
	function GetRequestClasses()
	{
		$q="SELECT * FROM ".$this->DESK->Database->Table("requestclass");
		$r=$this->DESK->Database->Query($q);
		$out=array();
		while ($row=$this->DESK->Database->FetchAssoc($r))
			$out[$row['classid']]=$row;
		$this->DESK->Database->Free($r);
		return $out;
	}
	
	/**
	 * Save/Create a Request Class
	 * @param string $classname Name
	 * @param string $classclass Class class (concrete request class)
	 * @param int $id (optional, if present will save otherwise create)
	**/
	function SaveRequestClass($classname, $classclass, $id=0)
	{
		if ($id == 0)
		{
			$q="INSERT INTO ".$this->DESK->Database->Table("requestclass")."(";
			$q.=$this->DESK->Database->Field("classname").",".$this->DESK->Database->Field("classclass").") ";
			$q.="VALUES(";
			$q.=$this->DESK->Database->SafeQuote($classname).",".$this->DESK->Database->SafeQuote($classclas).")";
			$this->DESK->Database->Query($q);
		}
		else
		{
			$q="UPDATE ".$this->DESK->Database->Table("requestclass")." SET ";
			$q.=$this->DESK->Database->Field("classname")."=".$this->DESK->Database->SafeQuote($classname).",";
			$q.=$this->DESK->Database->Field("classclass")."=".$this->DESK->Database->SafeQuote($classclass)." ";
			$q.="WHERE ".$this->DESK->Database->Field("classid")."=".$this->DESK->Database->Safe($id);
			$this->DESK->Database->Query($q);
		}
	}
	
	/**
	 * Delete a request class
	 * @param int $id Request Class ID
	**/
	function DeleteRequestClass($id)
	{
		$q="DELETE FROM ".$this->DESK->Database->Table("requestclass")." WHERE ";
		$q.=$this->DESK->Database->Field("classid")."=".$this->DESK->Database->Safe($id);
		$this->DESK->Database->Query($q);
	}
	
	/**
	 * Get a priority list
	 * @return array Priorities
	**/
	function GetPriorityList()
	{
		$q="SELECT * FROM ".$this->DESK->Database->Table("priority");
		$r=$this->DESK->Database->Query($q);
		$out=array();
		while ($row=$this->DESK->Database->FetchAssoc($r))
		{
			$out[$row['priorityid']]=$row;
		}
		$this->DESK->Database->Free($r);
		return $out;
	}
	
	/**
	 * Save/Create a Request Priority
	 * @param string $priorityname Name
	 * @param int $resolutionsla Resolution SLA (seconds)
	 * @param int $schedule Schedule ID for SLA
	 * @param int $priorityid Priority ID (optional)
	**/
	function SavePriority($priorityname, $resolutionsla, $schedule, $priorityid=0)
	{
		if ($priorityid == 0)
		{
			$q="INSERT INTO ".$this->DESK->Database->Table("priority")." (";
			$q.=$this->DESK->Database->Field("priorityname").",".$this->DESK->Database->Field("resolutionsla").",";
			$q.=$this->DESK->Database->Field("schedule").") VALUES(";
			$q.=$this->DESK->Database->SafeQuote($priorityname).",".$this->DESK->Database->Safe($resolutionsla).",".$this->DESK->Database->Safe($schedule).")";
			$this->DESK->Database->Query($q);
		}
		else
		{
			$q="UPDATE ".$this->DESK->Database->Table("priority")." SET ";
			$q.=$this->DESK->Database->Field("priorityname")."=".$this->DESK->Database->SafeQuote($priorityname).",";
			$q.=$this->DESK->Database->Field("resolutionsla")."=".$this->DESK->Database->Safe($resolutionsla).",";
			$q.=$this->DESK->Database->Field("schedule")."=".$this->DESK->Database->Safe($schedule)." ";
			$q.="WHERE ".$this->DESK->Database->Field("priorityid")."=".$this->DESK->Database->Safe($priorityid);
			$this->DESK->Database->Query($q);
		}
	}
	
	/**
	 * Delete a priority
	 * @param int $priorityid Priority ID
	**/
	function DeletePriority($priorityid)
	{
		$q="DELETE FROM ".$this->DESK->Database->Table("priority")." WHERE ".$this->DESK->Database->Field("priorityid")."=".$this->DESK->Database->Safe($priorityid);
		$this->DESK->Database->Query($q);
	}
	
	
	
}
?>
