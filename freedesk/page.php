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
 * Display HTML page components
**/


// Output buffer on and start FreeDESK then discard startup whitespace-spam
ob_start();
include("core/FreeDESK.php");
$DESK = new FreeDESK("./");
$DESK->Start();
ob_end_clean();
header("Expires: Tue, 27 Jul 1997 01:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


if ( (!isset($_REQUEST['sid'])) || (!$DESK->ContextManager->Open(ContextType::User, $_REQUEST['sid'])) )
{
	echo $DESK->Lang->Get("login.expire");
	exit();
}

$page = $DESK->PluginManager->GetPage($_REQUEST['page']);

if ($page === false)
{
	if ($DESK->PluginManager->PIMPage($_REQUEST['page']))
		exit();
		
	echo "404: ".$DESK->Lang->Get("not_found")." ".$_REQUEST['page'];
	exit();
}

$perm = "page.".$_REQUEST['page'];

if ($DESK->PermissionManager->PermissionExists($perm))
{
	// This page has its own permission rule
	if (!$DESK->ContextManager->Permission($perm))
	{
		echo ErrorCode::Forbidden.": ".$DESK->Lang->Get("permission_denied");
		exit();
	}
}

include($page);


?>
