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
		$this->DESK->PluginManager->Register(new Plugin(
			"Email System", "0.01", "Core" ));
		$this->DESK->Include->IncludeFile("core/phpmailer/class.phpmailer.php");
		$this->DESK->Include->IncludeFile("core/phpmailer/class.smtp.php");
		$this->DESK->PluginManager->Register(new Plugin(
			"phpMailer", "5.2.1", "Email" ));
		$this->DESK->PermissionManager->Register("email_accounts",false);
		$this->DESK->PermissionManager->Register("email_templates",false);
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
				$this->accounts[$row['accountid']] = $row;
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
	
	/**
	 * Get email accounts
	 * @return array Email account data
	**/
	function GetAccounts()
	{
		if (!$this->loaded)
			$this->LoadAccounts();
		return $this->accounts;
	}
	
	/**
	 * Save/Create Email account
	 * @param string $name Account name
	 * @param string $host SMTP host
	 * @param string $from From email
	 * @param string $fromName From Name
	 * @param int $wordwrap Word Wrap
	 * @param int $auth Use Authentication
	 * @param string $username Username
	 * @param string $password Password
	 * @param string $smtpsec SMTP Security Mode
	 * @param int $id Account ID (optional, default 0=create new)
	**/
	function SaveAccount($name, $host, $from, $fromName, $wordwrap, $auth, $username,
		$password, $smtpsec, $id=0)
	{
		if ($id == 0)
		{
			$q="INSERT INTO ".$this->DESK->Database->Table("email")."(";
			$q.=$this->DESK->Database->Field("name").",";
			$q.=$this->DESK->Database->Field("host").",";
			$q.=$this->DESK->Database->Field("from").",";
			$q.=$this->DESK->Database->Field("fromname").",";
			$q.=$this->DESK->Database->Field("wordwrap").",";
			$q.=$this->DESK->Database->Field("auth").",";
			$q.=$this->DESK->Database->Field("username").",";
			$q.=$this->DESK->Database->Field("password").",";
			$q.=$this->DESK->Database->Field("smtpsec").") ";
			$q.="VALUES(";
			$q.=$this->DESK->Database->SafeQuote($name).",";
			$q.=$this->DESK->Database->SafeQuote($host).",";
			$q.=$this->DESK->Database->SafeQuote($from).",";
			$q.=$this->DESK->Database->SafeQuote($fromName).",";
			$q.=$this->DESK->Database->Safe($wordwrap).",";
			$q.=$this->DESK->Database->Safe($auth).",";
			$q.=$this->DESK->Database->SafeQuote($username).",";
			$q.=$this->DESK->Database->SafeQuote($password).",";
			$q.=$this->DESK->Database->SafeQuote($smtpsec).")";
			
			$this->DESK->Database->Query($q);
		}
		else
		{
			$q="UPDATE ".$this->DESK->Database->Table("email")." SET ";
			$q.=$this->DESK->Database->Field("name")."=".$this->DESK->Database->SafeQuote($name).",";
			$q.=$this->DESK->Database->Field("host")."=".$this->DESK->Database->SafeQuote($host).",";
			$q.=$this->DESK->Database->Field("from")."=".$this->DESK->Database->SafeQuote($from).",";
			$q.=$this->DESK->Database->Field("fromname")."=".$this->DESK->Database->SafeQuote($fromName).",";
			$q.=$this->DESK->Database->Field("wordwrap")."=".$this->DESK->Database->Safe($wordwrap).",";
			$q.=$this->DESK->Database->Field("auth")."=".$this->DESK->Database->Safe($auth).",";
			$q.=$this->DESK->Database->Field("username")."=".$this->DESK->Database->SafeQuote($username).",";
			$q.=$this->DESK->Database->Field("password")."=".$this->DESK->Database->SafeQuote($password).",";
			$q.=$this->DESK->Database->Field("smtpsec")."=".$this->DESK->Database->SafeQuote($smtpsec)." ";
			$q.="WHERE ";
			$q.=$this->DESK->Database->Field("accountid")."=".$this->DESK->Database->Safe($id);
			
			$this->DESK->Database->Query($q);
		}
	}
	
	/**
	 * Delete Email account
	 * @param int $id Account ID
	**/
	function DeleteAccount($id)
	{
		$q="DELETE FROM ".$this->DESK->Database->Table("email")." WHERE ".$this->DESK->Database->Field("accountid")."=".$this->DESK->Database->Safe($id);
		$this->DESK->Database->Query($q);
	}
	
	/**
	 * Send an email
	 * @param int $id Account ID
	 * @param string $to To Address
	 * @param string $subject Subject
	 * @param string $body Body
	 * @return bool Indicates successfully sent
	**/
	function Send($id, $to, $subject, $body)
	{
		if (!$this->loaded)
			$this->LoadAccounts();
			
		if (!isset($this->accounts[$id]))
			return false;
		$acc = $this->accounts[$id];
		
		$mail = new PHPMailer();
		
		//$mail->SMTPDebug=2;
		
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AddAddress($to);
		$mail->SetFrom($acc['from'], $acc['fromname']);
		$mail->WordWrap = $acc['wordwrap'];
		
		if ($acc['host'] != "")
		{
			$mail->IsSMTP();
			if (strpos($acc['host'],":") !== false)
			{
				$parts = explode(":", $acc['host']);
				$mail->Host=$parts[0];
				$mail->Port=$parts[1];
			}
			else
				$mail->Host=$acc['host'];
			if ($acc['auth'] == 1)
			{
				$mail->SMTPAuth = true;
				$mail->Username = $acc['username'];
				$mail->Password = $acc['password'];
			}
			if ($acc['smtpsec'] != "")
			{
				$mail->SMTPSecure = $acc['smtpsec'];
			}
		}
		
		//print_r($mail);
		
		if (!$mail->Send()) // Failed
		{
			$this->DESK->LoggingEngine->Log("Email Failed to ".$to, "Email", "Fail", 2);
			$this->DESK->LoggingEngine->Log("phpMailer: ".$mail->ErrorInfo,"Email","Error", 2);
			return false;
		}
		else
		{
			$this->DESK->LoggingEngine->Log("Email Sent to ".$to, "Email", "Send", 8);
			return true;
		}
	}
	
	/**
	 * Load Templates
	 * @param bool $force Force reload (optional, default false)
	**/
	private function LoadTemplates($force=false)
	{
		if (!$this->loadedTemplates || $force)
		{
			$this->templates = array();
			$q="SELECT * FROM ".$this->DESK->Database->Table("templates");
			$r=$this->DESK->Database->Query($q);
			while ($row=$this->DESK->Database->FetchAssoc($r))
			{
				$this->templates[$row['templateid']] = $row;
			}
			$this->loadedTemplates = true;
		}
	}
	
	/**
	 * Save (or create) a template
	 * @param string $id Template ID
	 * @param string $subject Subject
	 * @param string $body Body
	**/
	function SaveTemplate($id, $subject, $body)
	{
		$this->LoadTemplates();
		// Check if it exists
		if (isset($this->templates[$id]))
		{
			$q="UPDATE ".$this->DESK->Database->Table("templates")." SET ";
			$q.=$this->DESK->Database->Field("subject")."=".$this->DESK->Database->SafeQuote($subject).",";
			$q.=$this->DESK->Database->Field("body")."=".$this->DESK->Database->SafeQuote($body)." ";
			$q.="WHERE ".$this->DESK->Database->Field("templateid")."=".$this->DESK->Database->SafeQuote($id);
			$this->DESK->Database->Query($q);
		}
		else
		{
			$q="INSERT INTO ".$this->DESK->Database->Table("templates")."(";
			$q.=$this->DESK->Database->Field("templateid").",";
			$q.=$this->DESK->Database->Field("subject").",";
			$q.=$this->DESK->Database->Field("body").")";
			$q.=" VALUES(";
			$q.=$this->DESK->Database->SafeQuote($id).",";
			$q.=$this->DESK->Database->SafeQuote($subject).",";
			$q.=$this->DESK->Database->SafeQuote($body).")";
			$this->DESK->Database->Query($q);
		}
		$this->LoadTemplates(true);
	}
	
	/**
	 * Get a template by ID
	 * @param string $id ID
	 * @return mixed String of template on success or bool false on failure
	**/
	function GetTemplate($id)
	{
		$this->LoadTemplates();
		
		if (isset($this->templates[$id]))
			return $this->templates[$id];
		return false;
	}
	
	/**
	 * Get a substituted template by ID
	 * @param string $id ID
	 * @param array $data Data array (macro => value)
	 * @return mixed String of sub'd template on success or bool false on failure
	**/
	function GetSubTemplate($id, $data)
	{
		$temp = $this->GetTemplate($id);
		if ($temp === false)
			return false;
		foreach($data as $key => $val)
		{
			$macro = "%".$key."%";
			$temp['subject'] = str_replace($macro, $val, $temp['subject']);
			$temp['body'] = str_replace($macro, $val, $temp['body']);
		}
		return $temp;
	}
}
?>
