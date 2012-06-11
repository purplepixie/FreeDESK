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

$q="SELECT ".$DESK->Database->Field("username").",".$DESK->Database->Field("realname")." FROM ".$DESK->Database->Table("user");
$r=$DESK->Database->Query($q);
$users=array();
while($row=$DESK->Database->FetchAssoc($r))
{
	$users[$row['username']] = $row['realname'];
}
$DESK->Database->Free($r);

$q="SELECT * FROM ".$DESK->Database->Table("team");
$r=$DESK->Database->Query($q);
$team=array();
while($row=$DESK->Database->FetchAssoc($r))
{
	$team[$row['teamid']]=$row['teamname'];
}

$q="SELECT * FROM ".$DESK->Database->Table("teamuserlink");
$r=$DESK->Database->Query($q);
$teamlink=array();
while($row=$DESK->Database->FetchAssoc($r))
{
	if (isset($teamlink[$row['teamid']]))
		$teamlink[$row['teamid']][]=$row['username'];
	else
		$teamlink[$row['teamid']]=array( $row['username'] );
}

echo "<ul class=\"leftpane\">\n";
foreach($team as $teamid => $teamname)
{
	echo "<li><a href=\"#\">".$teamname."</a>";
	if (isset($teamlink[$teamid]))
	{
		echo "\n<ul class=\"leftpane_sub\">\n";
		foreach($teamlink[$teamid] as $username)
		{
			echo "<li><a href=\"#\">".$users[$username]."</a></li>\n";
		}
		echo "</ul>\n";
	}
	echo "</li>\n";
}

echo "<li>All Users\n";
echo "<ul class=\"leftpane_sub\">";
foreach($users as $username => $realname)
{
	echo "<li><a href=\"#\">".$realname."</a></li>\n";
}
echo "</ul>";


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
