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

$panes = array(
	"log" => array( "title" => "Request History" ),
	"details" => array( "title" => "Details" ),
	"update" => array( "title" => "Update Request" ) );

$data = array( "id" => "request", "panes" => $panes );
$DESK->Skin->IncludeFile("pane_start.php", $data);

echo "<div id=\"pane_request_log_content\" class=\"pane_content\">\n";
echo "Log";
echo "</div>";

echo "<div id=\"pane_request_details_content\" class=\"pane_content_hidden\">\n";
echo "Details";
echo "</div>";

echo "<div id=\"pane_request_update_content\" class=\"pane_content_hidden\">\n";
echo "Update";
echo "</div>";

$DESK->Skin->IncludeFile("pane_finish.php");

$DESK->Skin->IncludeFile("min_footer.php");


?>
