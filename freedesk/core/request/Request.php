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
 * Request class for all standard requests
**/
class Request extends RequestBase
{
	
	/**
	 * Constructor
	 * @param mixed &$freeDESK Current FreeDESK instance
	**/
	function Request(&$freeDESK)
	{
		parent::RequestBase($freeDESK);
	}
	
	/**
	 * Create a request
	 * @param int $customer Customer ID
	 * @param string $update Initial Update
	 * @param int $class Request Class
	 * @param int $status Initial request status
	 * @param int $group Request Group (optional, default 0)
	 * @param string $assign Assigned user (optional, default "")
	 * @return string Request ID
	**/
	function Create($customer, $update, $class, $status, $group=0, $assign="")
	{
		$q="INSERT INTO ".$this->DESK->Database->Table("request");
		$q.="(".$this->DESK->Database->Field("customer").",";
		$q.=$this->DESK->Database->Field("assignteam").",";
		$q.=$this->DESK->Database->Field("assignuser").",";
		$q.=$this->DESK->Database->Field("class").",";
		$q.=$this->DESK->Database->Field("openeddt").",";
		$q.=$this->DESK->Database->Field("status").") ";
		$q.="VALUES(";
		$q.=$this->DESK->Database->Safe($customer).",";
		$q.=$this->DESK->Database->Safe($group).",";
		$q.=$this->DESK->Database->SafeQuote($assign).",";
		$q.=$this->DESK->Database->SafeQuote($class).",";
		$q.="NOW(),";
		$q.=$this->DESK->Database->Safe($status).")";
		
		$this->DESK->Database->Query($q);
		
		$this->ID = $this->DESK->Database->InsertID();
		
		$this->Update($update, true);
		
		return $this->ID;
	}
	
	/**
	 * Update a request
	 * @param string $update Update text
	 * @param bool $public Public update (optional, default false)
	**/
	function Update($update, $public=false)
	{
		if ($this->ID <= 0)
			return false;
		if ($this->DESK->ContextManager->Session == null)
			return false;
		
		$q="INSERT INTO ".$this->DESK->Database->Table("update")."(";
		$q.=$this->DESK->Database->Field("requestid").",";
		$q.=$this->DESK->Database->Field("update").",";
		$q.=$this->DESK->Database->Field("public").",";
		$q.=$this->DESK->Database->Field("updateby").",";
		$q.=$this->DESK->Database->Field("updatedt").") ";
		$q.="VALUES(";
		$q.=$this->DESK->Database->Safe($this->ID).",";
		$q.=$this->DESK->Database->SafeQuote($update).",";
		if ($public)
			$p=1;
		else
			$p=0;
		$q.=$p.",";
		$q.="\"".$this->DESK->ContextManager->Session->NiceName()."\",";
		$q.="NOW())";
		
		$this->DESK->Database->Query($q);
	}
	
	/**
	 * Change a request status
	 * @param int $status New status
	 * @param bool $public Public update (optional, default false)
	**/
	function Status($status, $public=false)
	{
		//
	}
	
	/**
	 * Assign a request
	 * @param int $group Group ID
	 * @param string $username Username (optional, default "")
	 * @param bool $public Public update (optional, default false)
	**/
	function Assign($group, $username="", $public=false)
	{
		$q="UPDATE ".$this->DESK->Database->Table("request")." SET ";
		$q.=$this->DESK->Database->Field("assignteam")."=".$this->DESK->Database->Safe($group).",";
		$q.=$this->DESK->Database->Field("assignuser")."=".$this->DESK->Database->SafeQuote($username)." WHERE ";
		$q.=$this->DESK->Database->Field("requestid")."=".$this->DESK->Database->Safe($this->ID);
		$this->DESK->Database->Query($q);
		
		$teams = $this->DESK->RequestManager->TeamList();
		$users = $this->DESK->RequestManager->UserList();
		$assign = "";
		
		if (isset($teams[$group]))
			$assign.=$teams[$group];
		else if ($group == 0)
			$assign.=$this->DESK->Lang->Get("unassigned");
		
		if ($username != "" && isset($users[$username]))
		{
			if ($assign != "")
				$assign.=" &gt; ";
			$assign.=$users[$username];
		}
		
		$update = $this->DESK->Lang->Get("assigned_to")." ".$assign;
		
		$this->Update($update, $public);
	}
	
	/**
	 * Attach a file
	 * @param int $fileid File ID
	 * @param bool $public Public update (optional, default false)
	**/
	function Attach($fileid, $public=false)
	{
		//
	}
	
	/**
	 * Output XML
	 * @return string xml output
	 * @param bool $header Put XML header on output (optional, default false)
	**/
	function XML($header=false)
	{
		$data = $this->Entity->GetData();
		$xml = new xmlCreate();
		$xml->startElement("request");
		$xml->xmlArray($data);
		$xml->endElement("request");
		return $xml->getXML($header);
	}
	
	
	
}
?>
