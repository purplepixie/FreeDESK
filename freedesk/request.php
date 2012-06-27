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
 * Request Display
**/


// Output buffer on and start FreeDESK then discard startup whitespace-spam
ob_start();
include("core/FreeDESK.php");
$DESK = new FreeDESK("./");
$DESK->Start();
ob_end_clean();


if (!isset($_REQUEST['sid']) || !$DESK->ContextManager->Open(ContextType::User, $_REQUEST['sid']))
{
	$data=array("title"=>$DESK->Lang->Get("welcome"));
	$DESK->Skin->IncludeFile("min_header.php",$data);

	echo "\n<noscript>\n";
	echo "<h1>Sorry you must have Javascript enabled to use FreeDESK analyst portal</h1>\n";
	echo "</noscript>\n";

	echo "<h3>".$DESK->Lang->Get("login_invalid").":</h3>\n";

	
	$DESK->Skin->IncludeFile("min_footer.php");
	exit();
}


// So we're authenticated let's view the main page
$data=array("title"=>"FreeDESK");
$DESK->Skin->IncludeFile("min_header.php",$data);

if (isset($_REQUEST['id']))
{
$id=$_REQUEST['id'];

$request = $DESK->RequestManager->Fetch($id);

if ($request === false)
{
	echo $DESK->Lang->Get("entity_not_found");
	$DESK->Skin->IncludeFile("min_footer.php");
	exit();
}

$request->LoadUpdates();

$panes = array(
	"log" => array( "title" => "Request History" ),
	"details" => array( "title" => "Details" ),
	"update" => array( "title" => "Update Request" ) );

$data = array( "id" => "request", "panes" => $panes );
$DESK->Skin->IncludeFile("pane_start.php", $data);

echo "<div id=\"pane_request_log_content\" class=\"pane_content\">\n";

$updates = $request->GetUpdates();

foreach($updates as $update)
{
	echo "<div id=\"update_".$update['updateid']."\" class=\"update\">\n";
	echo "<div id=\"update_header_".$update['updateid']."\" class=\"update_header\">\n";
	echo $update['updatedt']." : ".$update['updateby']."\n";
	echo "</div>\n";
	echo "<div id=\"update_content_".$update['updateid']."\" class=\"update_content\">";
	echo $update['update']."\n\n";
	echo "</div>\n";
	echo "</div>\n";
}

echo "</div>";

echo "<div id=\"pane_request_details_content\" class=\"pane_content_hidden\">\n";
echo "Details";
echo "</div>";

echo "<div id=\"pane_request_update_content\" class=\"pane_content_hidden\">\n";

echo "<form id=\"request_update\" onsubmit=\"return false;\">";

echo "<table class=\"request_update\">\n";

echo "<tr><td colspan=\"2\">\n";

echo "<input type=\"hidden\" name=\"mode\" value=\"request_update\">\n";
echo "<input type=\"hidden\" name=\"requestid\" value=\"".$id."\">\n";
echo "<textarea rows=\"10\" cols=\"50\" name=\"update\"></textarea>\n";

echo "</td></tr>\n";
echo "<tr><td>";

echo $DESK->Lang->Get("assign")." ";

echo "</td><td>";

echo "<select name=\"assign\">\n";

echo "<option value=\" \" selected>".$DESK->Lang->Get("no_change")."</option>\n";

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

echo "</td></tr>\n";

echo "<tr><td>\n";

echo $DESK->Lang->Get("status");

echo "</td><td>\n";

$statuses = $DESK->RequestManager->StatusList();

echo "<select name=\"status\">\n";
echo "<option value=\" \" selected>".$DESK->Lang->Get("no_change")."</option>\n";

foreach($statuses as $code => $desc)
	echo "<option value=\"".$code."\">".$desc."</option>\n";

echo "</select>\n";
echo "</td></tr>\n";

echo "<tr><td>\n";

echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("save")."\" onclick=\"DESK.formAPI('request_update',false,true);\">";

echo "</td><td>\n";

echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("save_close")."\" onclick=\"DESK.formAPI('request_update',true,false);\">";

echo "</td></tr>\n";

echo "</table>";

echo "</form>\n";

echo "</div>";



$DESK->Skin->IncludeFile("pane_finish.php");
}
else // new request
{

echo "<div id=\"customer_select\" class=\"customer_select\">\n";

echo "<form id=\"customersearch\" onsubmit=\"return false;\">\n";
echo "<table class=\"search\">\n";

$table=$DESK->DataDictionary->Tables["customer"];

foreach($table->fields as $id => $field)
{
	if ($field->searchable)
	{
		echo "<tr><td>".$field->name."</td>\n";
		$val="";
		if (isset($_REQUEST[$field->field]))
		{
			$val=$_REQUEST[$field->field];
			$searchnow=true;
		}
		echo "<td><input type=\"text\" name=\"".$field->field."\" value=\"".$val."\" /></td></tr>\n";
	}
}
echo "<tr><td> </td>\n";
echo "<td><input type=\"submit\" value=\"".$DESK->Lang->Get("search")."\" onclick=\"DESKRequest.searchCustomer();\" /></td>\n";
echo "</tr>";
echo "</table>\n";
echo "</form>\n";


echo "</div>";
echo "<div id=\"customer_details\" class=\"customer_details\">\n";
echo "<br /><b>".$DESK->Lang->Get("customer")." : </b><span id=\"customer_id\"></span> \n";
echo "<a href=\"#\" onclick=\"DESKRequest.searchCustomerAgain();\">Change</a>";
echo "</div>";

echo "<hr class=\"request\" />\n";

echo "<form id=\"request_create\" onsubmit=\"return false;\">";

echo "<table class=\"request_update\">\n";

echo "<tr><td colspan=\"2\">\n";

echo "<textarea rows=\"10\" cols=\"50\" name=\"update\"></textarea>\n";

echo "</td></tr>\n";
echo "<tr><td>";

echo $DESK->Lang->Get("assign")." ";

echo "</td><td>";

echo "<select name=\"assign\">\n";

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

echo "</td></tr>\n";

echo "<tr><td>\n";

echo $DESK->Lang->Get("status");

echo "</td><td>\n";

$statuses = $DESK->RequestManager->StatusList();

echo "<select name=\"status\">\n";

foreach($statuses as $code => $desc)
	echo "<option value=\"".$code."\">".$desc."</option>\n";

echo "</select>\n";
echo "</td></tr>\n";

echo "<tr><td>\n";

echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("save")."\" onclick=\"DESKRequest.Create();\" />";

echo "</td><td>\n";

echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("save_close")."\" onclick=\"DESKRequest.Create(true);\" />";

echo "</td></tr>\n";

echo "</table>";

echo "</form>";

}



$DESK->Skin->IncludeFile("min_footer.php");


?>
