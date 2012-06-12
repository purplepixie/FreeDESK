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
/*
<div id="page_menu" class="page_menu">
<ul id="menu">
 <li><a href="#" onclick="DESK.displayMain(true);">Home</a></li>
 <li><a href="#">Pages</a>
 <ul>
  <li><a href="#" onclick="DESK.loadSubpage('debug');">Debug</a></li>
  <li><a href="#">A.N.Other</a></li>
  <li><a href="#">A.N.Other</a></li>
  <li><a href="#">A.N.Other</a></li>
  </ul></li>
</ul>
</div>
<br />
*/
function MenuOptionPrint($menuitem,$close=true)
{
	echo "<li><a href=\"".$menuitem->link."\"";
	if ($menuitem->onclick != "")
	{
		echo " onclick=\"".$menuitem->onclick."\"";
	}
	echo ">";
	echo $menuitem->display;
	echo "</a>";
	if ($close)
		echo "</li>";
	echo "\n";
}

global $DESK;
$items = $DESK->ContextManager->MenuItems();
if ($items===false)
{
	// nothing here
}
else
{
	echo "<div id=\"page_menu\" class=\"page_menu\">\n";
	echo "<ul id=\"menu\">";
	foreach($items as $item)
	{
		if (is_array($item->submenu) && (sizeof($item->submenu)>0))
		{
			MenuOptionPrint($item, false);
			echo "<ul>\n";
			foreach($item->submenu as $subitem)
				MenuOptionPrint($subitem);
			echo "</ul>\n";
			echo "</li>\n";
		}
		else
		{
			MenuOptionPrint($item);
		}
	}
	echo "</ul>\n";
	echo "</div>\n";
}
?>
