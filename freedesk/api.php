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

ob_start();
include("core/FreeDESK.php");
$DESK = new FreeDESK("./");
$DESK->Start();
ob_end_clean();

header("Content-type: text/xml");
header("Expires: Tue, 27 Jul 1997 01:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_REQUEST['mode']))
{
	$error = new FreeDESK_Error(ErrorCode::UnknownMode, "Unknown Mode");
	echo $error->XML(true);
	exit();
}

if ($_REQUEST['mode']=="login")
{
	//echo $_REQUEST['username'].$_REQUEST['password'];
	// TODO: Other Login Modes
	if ($DESK->ContextManager->Open(ContextType::User, "", $_REQUEST['username'], $_REQUEST['password']))
	{
		echo $DESK->ContextManager->Session->XML(true);
		exit();
	}
	else // Login failed
	{
		$error = new FreeDESK_Error(ErrorCode::FailedLogin, "Login Failed");
		echo $error->XML(true);
		exit();
	}
}
else if ($_REQUEST['mode']=="logout")
{
	if ($DESK->ContextManager->Open(ContextType::User, $_REQUEST['sid']))
		$DESK->ContextManager->Destroy();
	$xml = new xmlCreate();
	$xml->charElement("logout","1");
	echo $xml->getXML(true);
	exit();
}

if (!$DESK->ContextManager->Open(ContextType::User, $_REQUEST['sid']))
{
	$error = new FreeDESK_Error(ErrorCode::SessionExpired, "Session Expired");
	echo $error->XML(true);
	exit();
}

if ($_REQUEST['mode']=="requests_assigned")
{
	$team = isset($_REQUEST['teamid']) ? $_REQUEST['teamid'] : 0;
	$user = isset($_REQUEST['username']) ? $_REQUEST['username'] : "";
	$list = $DESK->RequestManager->FetchAssigned($team, $user);
	echo xmlCreate::getHeader()."\n";
	echo "<request-list>\n";
	foreach($list as $item)
	{
		echo $item->XML(false)."\n";
	}
	echo "</request-list>\n";
	exit();
}


$error = new FreeDESK_Error(ErrorCode::UnknownMode, "Unknown Mode");
echo $error->XML(true);
exit();

?>
