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
 * Main index (web interface) file
**/

// First check for the existance of setup.php and go there if it exists
if (file_exists("setup.php"))
{
	header("Location: setup.php");
	exit();
}

// Output buffer on and start FreeDESK then discard startup whitespace-spam
ob_start();
include("core/FreeDESK.php");
$DESK = new FreeDESK("./");
$DESK->Start();
ob_end_clean();


if (!isset($_REQUEST['sid']))
{
	$data=array("title"=>$DESK->Lang->Get("welcome"));
	$DESK->Skin->IncludeFile("header.php",$data);

	for ($i=0; $i<12; ++$i)
		echo "<div class=\"spacer\"><br /></div>\n";
	?>
	<script type="text/javascript">
	DESK.show_login();
	</script>
	<?php
	$DESK->Skin->IncludeFile("footer.php");
	exit();
}

// So we have a SID - check if it authenticates
if (!$DESK->ContextManager->Open(ContextType::User, $_REQUEST['sid']))
{
	header("Location: ./"); // login page redirect on failure
	exit();
}

// So we're authenticated let's view the main page
$data=array("title"=>"FreeDESK");
$DESK->Skin->IncludeFile("header.php",$data);

echo "<div id=\"mainpage\">\n";
$DESK->Include->IncludeFile("pages/main.php");
echo "</div>\n";
echo "<div id=\"subpage\"></div>\n";

$DESK->Skin->IncludeFile("footer.php");


?>
