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
if (!$DESK->ContextManager->Open(ContextType::Customer, $_REQUEST['sid']))
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
<link rel="stylesheet" type="text/css" href="customer.css" />
<title>FreeDESK Customer Interface</title>
</head>
<body>
<div class="container">

<div class="header">FreeDESK Customer Interface</div>
<div class="menu">
<a href="./?mode=new&sid=<?php echo $_REQUEST['sid']; ?>">New Request</a> | 
<a href="./?mode=myrequests&sid=<?php echo $_REQUEST['sid']; ?>">My Requests</a> | 
<a href="../">Logout</a>
</div>
<?php
if (!isset($_REQUEST['mode']))
{
?>
<div class="contents">
Welcome to the FreeDESK customer interface.<br /><br />
Please select an option from above.<br /><br />
</div>
<?php
}
else if ($_REQUEST['mode'] == "myrequests")
{
	$data = array();
	$data[]=array("field"=>"customer", "value"=>$DESK->ContextManager->Session->username);
	$pris = $DESK->RequestManager->GetPriorityList();
	$stats = $DESK->RequestManager->StatusList();
	$reqs = $DESK->RequestManager->SearchRequests($data);
	echo "<table class=\"requestlist\">\n";
	foreach($reqs as $req)
	{
		echo "<tr>\n";
		echo "<td><a href=\"./?mode=request&requestid=".$req['requestid']."&sid=".$_REQUEST['sid']."\">ID ".$req['requestid']."</a></td>\n";
		echo "<td>".$req['openeddt']."</td>\n";
		echo "<td>";
		if (isset($pris[$req['priority']]))
			echo $pris[$req['priority']]['priorityname'];
		else
			echo "-";
		echo "</td>\n";
		echo "<td>";
		if (isset($stats[$req['status']]))
			echo $stats[$req['status']];
		else
			echo "-";
		echo "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>";
}
else if ($_REQUEST['mode'] == "request")
{
	$req = $DESK->RequestManager->Fetch($_REQUEST['requestid']);
	$cid = $req->Get("customerid");
	//echo $cid;
	if ($req === false)
	{
		echo "<h3>Request not found</h3>";
	}
	else if ($cid != $DESK->ContextManager->Session->username) // not our request (naughty!)
	{
		echo "<h3>Access to request denied</h3>";
	}
	else
	{
		echo "<table class=\"reqdetails\">\n";
		echo "<tr>\n";
		echo "<td>ID</td>\n";
		echo "<td>".$req->ID."</td></tr>\n";
		echo "<tr>\n";
		echo "<td>Opened</td>\n";
		echo "<td>".$req->Get("openeddt")."</td></tr>\n";
		echo "</table>\n";
		
		echo "<br /><br />\n";
		
		$req->LoadUpdates();
		$updates=$req->GetUpdates();
		
		echo "<table class=\"requpdates\">\n";
		foreach($updates as $update)
		{
			echo "<tr><td class=\"updatehead\">";
			echo $update['updatedt']." : ".$update['updateby']."\n";
			echo "</td></tr>\n";
			echo "<tr><td>\n";
			echo nl2br($update['update']);
			echo "</td></tr>";
		}
		echo "</table>\n";
		
		echo "<br /><br />";
		
		if ($req->Get("status")>0)
		{
			echo "<form id=\"requestupdate\" action=\"./\" method=\"post\">\n";
			echo "<input type=\"hidden\" name=\"action\" value=\"updaterequest\" />\n";
			echo "<input type=\"hidden\" name=\"requestid\" value=\"".$_REQUEST['requestid']."\" />\n";
			echo "<input type=\"hidden\" name=\"mode\" value=\"request\" />\n";
			echo "<input type=\"hidden\" name=\"sid\" value=\"".$_REQUEST['sid']."\" />\n";
			echo "<h3>Update Request</h3>\n";
			echo "<textarea name=\"update\" rows=\"10\" cols=\"50\"></textarea><br />\n";
			echo "<input type=\"submit\" value=\"Update Request\" />\n";
			echo "</form>\n";
		}
		else
		{
			echo "<h3>Request is closed - contact Service Desk to reopen</h3>\n";
		}
		
		echo "<br /><br />";
	}
}
else if ($_REQUEST['mode'] == "new")
{
	echo "<h3>New Request</h3>\n";
	echo "Please enter as much information about the request as possible.<br /><br />";
	echo "<form id=\"requestcreate\" action=\"./\" method=\"post\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"createrequest\" />\n";
	echo "<input type=\"hidden\" name=\"sid\" value=\"".$_REQUEST['sid']."\" />\n";
	echo "<textarea name=\"update\" rows=\"10\" cols=\"50\"></textarea><br />\n";
	echo "<input type=\"submit\" value=\"Create Request\" />\n";
	echo "</form>\n";
}
?>
</div>
</body>
</html>

