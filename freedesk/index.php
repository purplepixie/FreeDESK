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
ob_end_clean();



$data=array("title"=>"Welcome to FreeDESK");
$DESK->Skin->IncludeFile("header.php",$data);
/*
echo "<div class=\"spacer\"><br /></div>\n";
echo "<div class=\"spacer\"><br /></div>\n";
echo "<div class=\"spacer\"><br /></div>\n";
echo "<div class=\"spacer\"><br /></div>\n";
echo "<div class=\"spacer\"><br /></div>\n";
echo "<div class=\"spacer\"><br /></div>\n";
echo "<div class=\"spacer\"><br /></div>\n";
echo "<div class=\"spacer\"><br /></div>\n";
echo "<div class=\"spacer\"><br /></div>\n";
echo "<div class=\"spacer\"><br /></div>\n";
*/
$DESK->Skin->IncludeFile("login.php"); // DEBUG:,array("errorflag"=>1));

?>
<script type="text/javascript">
document.getElementById("login_form").style.display = 'block';
document.getElementById('screen_backdrop').style.display = 'block';
</script>
<?php

$DESK->Skin->IncludeFile("footer.php");
?>
