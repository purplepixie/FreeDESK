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
ob_end_clean();

if (!isset($_REQUEST['mode']))
{
	$error = new FreeDESK_Error(ErrorCode::UnknownMode, "Unknown Mode");
	echo $error->XML(true);
	exit();
}

if ($_REQUEST['mode']=="login")
{
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

?>
