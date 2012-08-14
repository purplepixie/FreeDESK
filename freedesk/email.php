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
 * Send an email update to customer
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
$data=array("title"=>"FreeDESK Email");
$DESK->Skin->IncludeFile("min_header.php",$data);

$request = $DESK->RequestManager->Fetch($_REQUEST['request']);
//echo "<pre>";
//print_r($request);

$customerid = $request->Get("customerid");

$q="SELECT * FROM ".$DESK->Database->Table("customer")." WHERE ".$DESK->Database->Field("customerid")."=".$DESK->Database->Safe($customerid)." LIMIT 0,1";
$r=$DESK->Database->Query($q);

$customer = $DESK->Database->FetchAssoc($r)
 or die("wah");

$DESK->Database->Free($r);

echo "<form id=\"email\" onsubmit=\"return false;\">\n";
echo "<input type=\"hidden\" name=\"mode\" value=\"email_send\" />\n";
echo "<table>";

echo "<tr><td>".$DESK->Lang->Get("from")."</td>\n";

$accounts = $DESK->Email->GetAccounts();

echo "<td>\n";
echo "<select name=\"id\">\n";
foreach($accounts as $id => $acc)
{
	echo "<option value=\"".$id."\">".$acc['name']." &quot;".$acc['fromname']."&quot; &lt;".$acc['from']."&gt;</option>\n";
}
echo "</select>\n";
echo "</td></tr>\n";

$customername = $customer['firstname']." ".$customer['lastname'];
$requestid = $_REQUEST['request'];
$update = $_REQUEST['update'];

echo "<tr><td>".$DESK->Lang->Get("to")."</td>\n";
echo "<td><input type=\"text\" name=\"to\" value=\"".$customer['email']."\" size=\"50\" /></td></tr>\n";

$data = array(
	"customer" => $customername,
	"requestid" => $requestid,
	"update" => $update );

$message = $DESK->Email->GetSubTemplate($_REQUEST['template'], $data);

echo "<tr><td>".$DESK->Lang->Get("subject")."</td>\n";
echo "<td><input type=\"text\" name=\"subject\" value=\"".$message['subject']."\" size=\"50\" /></td></tr>\n";

echo "<tr><td colspan=\"2\">\n";
echo "<textarea name=\"body\" rows=\"15\" cols=\"60\">".$message['body']."</textarea></td></tr>\n";

echo "<tr><td>&nbsp;</td><td><input type=\"submit\" value=\"".$DESK->Lang->Get("send")."\" onclick=\"DESK.formAPI('email',true);\" />\n";
echo "</td></tr>\n";

echo "</table></form>\n";

$DESK->Skin->IncludeFile("min_footer.php");


?>
