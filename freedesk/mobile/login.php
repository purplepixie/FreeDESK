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
if (isset($_REQUEST['username']))
{
	ob_start();
	require("../core/FreeDESK.php");
	$DESK = new FreeDESK("../");
	$DESK->Start();
	ob_end_clean();
	if($DESK->ContextManager->Open(ContextType::User, "",
		$_REQUEST['username'], $_REQUEST['password']))
	{
		$sid=$DESK->ContextManager->Session->sid;
		header("Location: ./?sid=".$sid);
		exit();
	}
	else
	{
		header("Location: ./login.php?e=failed");
		exit();
	}
}
else if (isset($_REQUEST['logout']))
{
	ob_start();
	require("../core/FreeDESK.php");
	$DESK = new FreeDESK("../");
	$DESK->Start();
	ob_end_clean();
	if($DESK->ContextManager->Open(ContextType::User, $_REQUEST['sid']))
	{
		$DESK->ContextManager->Destroy();
		header("Location: login.php?e=logout");
		exit();
	}
	else
	{
		header("Location: ./login.php?e=logout");
		exit();
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width" />
<link rel="stylesheet" type="text/css" href="mobile.css" />
<title>FreeDESK Mobile Interface</title>
</head>
<body>
<div class="header">
FreeDESK Mobile
</div>
<div class="container">
<form id="customer_login" action="login.php" method="post">

<?php
if (isset($_REQUEST['e']))
{
	if ($_REQUEST['e'] == "expired")
		echo "<b>Session Expired, Please Relogin</b>";
	else if ($_REQUEST['e'] == "failed")
		echo "<b>Login Failed, Incorrect Username or Password</b>";
	else if ($_REQUEST['e'] == "logout")
		echo "<b>You Are Logged Out</b>";
	echo "<br /><br />";
}
?>

Username<br />
<input type="text" name="username" class="mobLogin" /><br /><br />
Password<br />
<input type="password" name="password" class="mobLogin" /><br />
<br /><br />
<input type="submit" value="Login to FreeDESK" class="mobLogin" />
</form>
<br /><br />


<form action="../" method="post">
<input type="hidden" name="mobileoverride" value="1" />
<input type="submit" value="Use Desktop Interface" class="mobLogin" />
</form>

</div>
</body>
</html>

