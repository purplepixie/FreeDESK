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
 * Debug page
**/
global $DESK;
echo "<div id=\"debug_info\">\n";
echo "<h3>FreeDESK Version: ".$DESK->FullVersion()." </h3><br />\n";

echo "<b>Sub-Components</b><br /><br />";
echo "<table>";
echo "<tr><th>Module Name</th><th>Version</th><th>Type / Sub-Type</th></tr>";
$plugs=$DESK->PluginManager->GetAll();
foreach($plugs as $plug)
{
	echo "<tr><td>".$plug->name."</td>";
	echo "<td>".$plug->version."</td>";
	echo "<td>".$plug->type." ";
	if ($plug->subtype != "")
		echo "/ ".$plug->subtype;
	echo "</td>";
	echo "</tr>";
}
echo "</table>\n";

echo "</div>\n";

//<script type="text/javascript">
//document.getElementById("debug_info").innerHTML += "blah";
//</script>

