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
	if($DESK->ContextManager->Open(ContextType::Customer, "",
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
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="customer.css" />
<title>FreeDESK Customer Interface</title>
</head>
<body>
<div class="header">
Login to FreeDESK Customer Interface
</div>
<div>
<form id="customer_login" action="login.php" method="post">

<?php
if (isset($_REQUEST['e']))
{
	if ($_REQUEST['e'] == "expired")
		echo "<b>Session Expired, Please Relogin</b>";
	else if ($_REQUEST['e'] == "failed")
		echo "<b>Login Failed, Incorrect Username or Password</b>";
	echo "<br /><br />";
}
?>
<table>
<tr><td>Username or Email</td>
<td><input type="text" name="username" /></td></tr>
<tr><td>Password</td>
<td><input type="password" name="password" /></td></tr>
<tr><td>&nbsp;</td>
<td><input type="submit" value="Login to FreeDESK" /></td></tr>
</table>

</form>
</div>
</body>
</html>

