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
 * Email Class - provides email sending functionality for FreeDESK
**/
class Email
{
	/**
	 * FreeDESK instance
	**/
	private $DESK = null;
	
	/**
	 * Account Data loaded flag
	**/
	private $loaded = false;
	
	/**
	 * Email account information
	**/
	private $accounts = array();
	
	/**
	 * Email account count
	**/
	private $accountCount = 0;
	
	/**
	 * Constructor
	 * @param mixed &$freeDESK FreeDESK instance
	**/
	function Email(&$freeDESK)
	{
		$this->DESK = &$freeDESK;
	}
	
	/**
	 * Load account information
	**/
	private function LoadAccounts()
	{
		if (!$this->loaded)
		{
			$q="SELECT * FROM ".$this->DESK->Database->Table("email");
			$r=$this->DESK->Database->Query($q);
			$this->accountCount = 0;
			$this->accounts = array();
			while ($row=$this->DESK->Database->FetchAssoc($r))
			{
				$this->accountCount++;
				$this->accounts[] = $row;
			}
			$this->DESK->Database->Free($r);
			$this->loaded = true;
		}
	}
	
	/**
	 * Check if there are email accounts
	 * @return bool Account indicator flag
	**/
	function hasAccounts()
	{
		if (!$this->loaded)
			$this->LoadAccounts();
		if ($this->accountCount > 0)
			return true;
		return false;
	}
}
?>
