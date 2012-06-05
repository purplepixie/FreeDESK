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
 * Default Skin Main Header
**/

?>
<!DOCTYPE html>
<html>
<head>
<?php
if (isset($data['title'])) $title=$data['title'];
else $title="FreeDESK";

echo "<title>".$title."</title>\n";

echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$DESK->Skin->GetWebLocation("css/main.css")."\" />\n";
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$DESK->Skin->GetWebLocation("css/menu.css")."\" />\n";

$DESK->Skin->CommonHeader();
?>
</head>
<body>
<?php
$DESK->Skin->CommonBody();
?>
<div class="pageframe" id="pageframe">

<div class="header" id="header">
<a href="http://freedesk.purplepixie.org/" target="top">
<?php
echo "<img src=\"".$DESK->Skin->GetWebLocation("images/logo-white-120.png")."\" border=\"0\" />\n";
?>
</a>
</div>

<?php
$DESK->Skin->IncludeFile("menu.php");
?>

<div class="content" id="content">
