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
 * System Administration Page
**/

global $DESK;

echo "<!DOCTYPE html>\n";

function sa_link($text,$opts=array())
{
	$opt = "";
	foreach($opts as $key => $item)
		$opt.=$key."=".$item."&";
	$out = "<a href=\"#\" onclick=\"DESK.loadSubpage('sysadmin'";
	if ($opt != "")
		$out.=",'".$opt."'";
	$out.=");\">".$text."</a>";
	return $out;
}


if (isset($_REQUEST['mode']))
	$mode=$_REQUEST['mode'];
else
	$mode="";
	
if ($mode == "")
{
	echo "<h3>".$DESK->Lang->Get("system_admin")."</h3>\n";
	echo sa_link($DESK->Lang->Get("admin_user"), array("mode"=>"user"))."<br />\n";
}

else if ($mode == "user")
{
	echo "<br />".sa_link("&lt;&lt; ".$DESK->Lang->Get("system_admin"))."<br />\n";
	echo "<h3>".$DESK->Lang->Get("admin_user")."</h3>\n";
	
	// TODO: PUT INTO CORE
	$q="SELECT username,realname FROM ".$DESK->Database->Table("user");
	$r=$DESK->Database->Query($q);
	while ($row=$DESK->Database->FetchAssoc($r))
	{
		$oa = array("mode" => "useredit", "username" => $row['username']);
		echo sa_link($row['username'].": ".$row['realname'], $oa)."<br />";
	}
	$DESK->Database->Free($r);
	
}
else if ($mode == "useredit")
{
	$q="SELECT * FROM ".$DESK->Database->Table("user")." WHERE ".$DESK->Database->Field("username")."=".$DESK->Database->SafeQuote($_REQUEST['username']);
	$r=$DESK->Database->Query($q);
	$row=$DESK->Database->FetchAssoc($r);
	$DESK->Database->Free($r);
	echo "<br />".sa_link("&lt;&lt; ".$DESK->Lang->Get("system_admin"))." \n";
	echo sa_link("&lt;&lt; ".$DESK->Lang->Get("admin_user"),array("mode"=>"user"))."<br />\n";
	
	echo "<h3>".$row['realname']."</h3>\n";
	
	echo "<form id=\"user_admin\" action=\"#\" onsubmit=\"return false;\">\n";
	echo "<table>\n";
	echo "<input type=\"hidden\" name=\"mode\" value=\"user_edit\">\n";
	echo "<input type=\"hidden\" name=\"original_username\" value=\"".$row['username']."\">\n";
	
	echo "<tr><td>\n";
	echo $DESK->Lang->Get("username");
	echo "</td><td>\n";
	echo "<input type=\"text\" name=\"username\" value=\"".$row['username']."\">\n";
	echo "</td></tr>\n";
	
	echo "<tr><td>\n";
	echo $DESK->Lang->Get("realname");
	echo "</td><td>\n";
	echo "<input type=\"text\" name=\"realname\" value=\"".$row['realname']."\">\n";
	echo "</td></tr>\n";
	
	echo "<tr><td>\n";
	echo $DESK->Lang->Get("email");
	echo "</td><td>\n";
	echo "<input type=\"text\" name=\"email\" value=\"".$row['email']."\">\n";
	echo "</td></tr>\n";
	
	$q="SELECT * FROM ".$DESK->Database->Table("teamuserlink")." WHERE ".$DESK->Database->Field("username")."=".$DESK->Database->SafeQuote($row['username']);
	$r=$DESK->Database->Query($q);
	$teams=array();
	while ($trow = $DESK->Database->FetchAssoc($r))
	{
		$teams[$trow['teamid']] = true;
	}
	$DESK->Database->Free($r);
	
	echo "<tr><td>\n";
	echo $DESK->Lang->Get("team_membership");
	echo "</td><td>\n";
	
	$tq="SELECT * FROM ".$DESK->Database->Table("team");
	$tr=$DESK->Database->Query($tq);
	$first=false;
	while ($trow = $DESK->Database->FetchAssoc($tr))
	{
		if ($first)
			$first=false;
		else echo "<br />";
		
		$s = "<input type=\"checkbox\" name=\"team[]\" value=\"".$trow['teamid']."\"";
		if (isset($teams[$trow['teamid']]))
			$s.=" checked";
		$s.="> ".$trow['teamname'];
		echo $s;
	}
	
	echo "</td></tr>\n";
	
	
	
	echo "<tr><td>\n";
	echo $DESK->Lang->Get("permgroup");
	echo "</td><td>\n";
	$pg = $row['permgroup'];
	if ($pg == "")
		$pg = 0;
	
	$groups = array ( 0 => "None" );
	
	$pgq="SELECT * FROM ".$DESK->Database->Table("permgroup");
	$pgr=$DESK->Database->Query($pgq);
	while($pgrow = $DESK->Database->FetchAssoc($pgr))
	{
		$groups[$pgrow['permgroupid']] = $pgrow['groupname'];
	}
	$DESK->Database->Free($pgr);
	
	echo "<select name=\"permgroup\">\n";
	foreach($groups as $id => $name)
	{
		$o = "<option value=\"".$id."\"";
		if ($id == $pg)
			$o.=" selected";
		$o.=">".$name."</option>\n";
		echo $o;
	}
	echo "</select>\n";
	
	echo "</td></tr>\n";
	
	echo "<tr><td>\n";
	echo $DESK->Lang->Get("password");
	echo "</td><td>\n";
	echo "<input type=\"text\" name=\"password\" value=\"\">\n";
	echo "</td></tr>\n";
	
	echo "<tr><td>&nbsp;</td>\n";
	echo "<td><input type=\"submit\" value=\"".$DESK->Lang->Get("save")."\" onclick=\"DESK.formAPI('user_admin');\"></td></tr>\n";
	
	echo "</table></form>";
	
	
}
else
{
	echo "<h3>".$DESK->Lang->Get("system_admin")."</h3>\n";
	echo ErrorCode::UnknownMode.": ".$DESK->Lang->Get("action_invalid")." (".$mode.")<br />\n";
	exit();
}
?>
