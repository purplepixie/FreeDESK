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

global $DESK;

$DESK->Skin->IncludeFile("main_start.php");

$DESK->Skin->IncludeFile("main_left_start.php");

//



$teamusers = $DESK->RequestManager->TeamUserList();

echo "<ul class=\"leftpane\">\n";
foreach($teamusers as $team)
{
	echo "<li>";
	if ($team['view']) echo "<a href=\"#".$team['id']."\" onclick=\"DESK.mainPane(".$team['id'].")\">";
	echo $team['name'];
	if ($team['view']) echo "</a>";
	if (is_array($team['items']) && (sizeof($team['items'])>0))
	{
		echo "\n<ul class=\"leftpane_sub\">\n";
		foreach($team['items'] as $member)
		{
			echo "<li>";
			if ($team['team'])
				$t=$team['id'];
			else
				$t=0;
			if ($member['view']) echo "<a href=\"#a-".$member['username']."\" onclick=\"DESK.mainPane(".$t.",'".$member['username']."')\">";
			echo $member['realname'];
			if ($member['view']) echo "</a>";
			echo "</li>\n";
		}
		echo "</ul>\n";
	}
	echo "</li>\n";
}
echo "</ul>\n";
//



$DESK->Skin->IncludeFile("main_left_finish.php");

$DESK->Skin->IncludeFile("main_right_start.php");
?>

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

<?php
$DESK->Skin->IncludeFile("main_right_finish.php");

$DESK->Skin->IncludeFile("main_finish.php");
?>
