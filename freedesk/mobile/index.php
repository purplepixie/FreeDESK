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


if (!isset($_REQUEST['sid']))
{
	header("Location: login.php");
	exit();
}
require("../core/FreeDESK.php");
$DESK = new FreeDESK("../");
$DESK->Start();
if (!$DESK->ContextManager->Open(ContextType::User, $_REQUEST['sid']))
{
	header("Location: login.php?e=expired");
	exit();
}

if (isset($_REQUEST['action']))
{
	if ($_REQUEST['action'] == "updaterequest")
	{
		$rid = $_REQUEST['requestid'];
		$req = $DESK->RequestManager->Fetch($rid);
		if ($rid !== false && $req->Get("customerid")==$DESK->ContextManager->Session->username)
		{
			$req->Update($_REQUEST['update']);
		}
	}
	else if ($_REQUEST['action'] == "createrequest")
	{
		$req = $DESK->RequestManager->CreateById("");
		$rid=$req->Create($DESK->ContextManager->Session->username,
			$_REQUEST['update'],
			"",
			1 );
		$loc="./?mode=request&requestid=".$rid."&sid=".$_REQUEST['sid'];
		header("Location: ".$loc);
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

<div class="header">FreeDESK Mobile</div>
<div class="container">

<?php
if (isset($_REQUEST['mode']) && $_REQUEST['mode']=="request")
{
	echo "<form action=\"./\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"sid\" value=\"".$_REQUEST['sid']."\" />\n";
	echo "<input type=\"submit\" value=\"&lt;&lt; Back to Request List &lt;&lt;\" class=\"mobLogin\" />\n";
	echo "</form>\n";
	echo "<br /><br />\n";
	
	$req = $DESK->RequestManager->Fetch($_REQUEST['requestid']);
	if ($req === false)
	{
		echo "<b>Request not found</b>";
	}
	else if ($req->Get("assignuser") == $DESK->ContextManager->Session->username)
	{
		echo "<b>ID ".$req->ID." for ".$req->Get("customer")."</b><br /><br />\n";
		$req->LoadUpdates();
		$updates = $req->GetUpdates();
		
		foreach($updates as $update)
		{
			echo "<div class=\"update\">\n";
			echo "<div class=\"update_header\">\n";
			echo $update['updatedt'].": ".$update['updateby']."\n";
			echo "</div>\n";
			echo "<div class=\"update_content\">\n";
			echo nl2br($update['update'])."\n";
			echo "</div>\n";
			echo "</div>\n";
		}
		
		echo "<h3>Update Request</h3>\n";
		echo "<form id=\"update_form\" action=\"./\" method=\"post\">\n";
		echo "<input type=\"hidden\" name=\"action\" value=\"update\" />\n";
		echo "<input type=\"hidden\" name=\"mode\" value=\"request\" />\n";
		echo "<input type=\"hidden\" name=\"requestid\" value=\"".$_REQUEST['requestid']."\" />\n";
		echo "<input type=\"hidden\" name=\"sid\" value=\"".$_REQUEST['sid']."\" />\n";
		echo "<textarea name=\"update\" class=\"update\"></textarea>\n";
		echo "<br /><br />\n";
		
		echo "Assign<br />\n";
		echo "<select name=\"assign\" class=\"update\">\n";
		echo "<option value=\"\" selected>No Change</option>\n";
		$list = $DESK->RequestManager->TeamUserList();
		foreach($list as $teamid => $team)
		{
			$teamname = $team['name'];
			if ($team['assign'])
				echo "<option value=\"".$teamid."\">".$teamname."</option>\n";
			if (is_array($team['items']))
			{
				foreach($team['items'] as $username => $detail)
				{
					if ($team['team'])
						$tid = $teamid;
					else
						$tid = 0;
					if ($detail['assign'])
						echo "<option value=\"".$tid."/".$username."\">".$teamname." &gt; ".$detail['realname']."</option>\n";
				}
			}
		}
		echo "</select>\n";
		
		echo "<br /><br />\n";
		
		echo "Status<br />\n";
		
		$statuses = $DESK->RequestManager->StatusList();
		
		echo "<select name=\"status\" class=\"update\">\n";
		echo "<option value=\"\" selected>No Change</option>\n";
		
		foreach($statuses as $code => $desc)
			echo "<option value=\"".$code."\">".$desc."</option>\n";
		
		echo "</select>\n";
		
		echo "<br /><br />\n";
		
		echo "<input type=\"submit\" class=\"mobLogin\" value=\"Update Request\" />\n";
		
		echo "<br /><br />\n";
		
		echo "</form>\n";
	}
	else
	{
		echo "<b>Sorry access to request denied</b>";
	}
	
	echo "<br /><br />\n";
	echo "<form action=\"./\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"sid\" value=\"".$_REQUEST['sid']."\" />\n";
	echo "<input type=\"submit\" value=\"&lt;&lt; Back to Request List &lt;&lt;\" class=\"mobLogin\" />\n";
	echo "</form>\n";
	echo "<br /><br />\n";
}
else
{
	$reqs = $DESK->RequestManager->FetchAssigned(0,
		$DESK->ContextManager->Session->username,
		"requestid");
	//echo sizeof($reqs);
	if (sizeof($reqs)<=0)
	{
		echo "<b>No requests assigned</b>\n";
	}
	
	echo "<table class=\"reqList\">\n";
	$row=0;
	
	foreach($reqs as $req)
	{
		echo "<tr class=\"row".$row."\">\n";
		if ($row==0)
			$row=1;
		else
			$row=0;
		echo "<td><a href=\"./?mode=request&requestid=".$req->ID."&sid=".$_REQUEST['sid']."\">".$req->ID."</a></td>\n";
		echo "<td>".$req->Get("customer")."</td>\n";
		echo "<td>\n";
		echo "<form action=\"./\" method=\"post\">\n";
		echo "<input type=\"hidden\" name=\"mode\" value=\"request\" />\n";
		echo "<input type=\"hidden\" name=\"requestid\" value=\"".$req->ID."\" />\n";
		echo "<input type=\"hidden\" name=\"sid\" value=\"".$_REQUEST['sid']."\" />\n";
		echo "<input type=\"submit\" value=\"Open\" class=\"openButton\" />\n";
		echo "</form>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
	
}
?>

<br /><br />
<form action="login.php" method="post">
<input type="hidden" name="mofr" value="logout" />
<input type="hidden" name="sid" value="<?php echo $_REQUEST['sid']; ?>" />
<input type="submit" value="Logout" class="mobLogin" />
</form>

<br /><br />
<form action="../" method="post">
<input type="hidden" name="mobileoverride" value="1" />
<input type="hidden" name="sid" value="<?php echo $_REQUEST['sid']; ?>" />
<input type="submit" value="Use Desktop Interface" class="mobLogin" />
</form>
</div>

</body>
</html>

