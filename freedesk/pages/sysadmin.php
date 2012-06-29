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

header("Content-type: text/xml");
header("Expires: Tue, 27 Jul 1997 01:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

echo "<!DOCTYPE html>\n";
echo "<html>\n";

if (!$DESK->ContextManager->Permission("system_admin"))
{
	echo "<h3>".$DESK->Lang->Get("permission_denied")."</h3>\n";
	echo "</html>\n";
	exit();
}

function sa_link($text,$opts=array())
{
	$opt = "";
	$first=true;
	foreach($opts as $key => $item)
	{
		if ($first)
			$first=false;
		else
			$opt.="&";
		$opt.=$key."=".$item;
	}
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
	
	if ($DESK->ContextManager->Permission("user_admin"))
	{
		echo sa_link($DESK->Lang->Get("admin_user"), array("mode"=>"user"))."<br /><br />\n";
		echo sa_link($DESK->Lang->Get("admin_group"), array("mode"=>"group"))."<br /><br />\n";
	}
	
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
		echo "<form id=\"delete_".$row['username']."\" onsubmit=\"return false;\">";
		
		echo "<input type=\"hidden\" name=\"mode\" value=\"delete_user\" />\n";
		echo "<input type=\"hidden\" name=\"username\" value=\"".$row['username']."\" />\n";
		echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("delete")."\" onclick=\"if (confirm('Delete user ".$row['username']."')) DESK.formAPI('delete_".$row['username']."',false,false,DESK.refreshSubpage);\" />\n";
		
		$oa = array("mode" => "useredit", "username" => $row['username']);
		echo sa_link($row['username'].": ".$row['realname'], $oa)." ";
		
		echo "</form>";
		
		//echo "<br />";
	}
	$DESK->Database->Free($r);
	
	echo "<br /><b>".$DESK->Lang->Get("user_create")."</b><br /><br />\n";
	echo "<form id=\"create_user\" onsubmit=\"return false;\">\n";
	echo "<input type=\"hidden\" name=\"mode\" value=\"create_user\">\n";
	echo $DESK->Lang->Get("username")." ";
	echo "<input type=\"text\" name=\"username\" value=\"\" /> \n";
	echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("save")."\" onclick=\"DESK.formAPI('create_user',false,false,DESK.refreshSubpage);\" />\n";
	echo "</form>\n";
	
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
	
	echo "<h3>".$DESK->Lang->Get("permissions")."</h3>\n";
	
	echo "<form id=\"permission_form\" onsubmit=\"return false;\">\n";
	echo "<input type=\"hidden\" name=\"mode\" value=\"permission_save\" />\n";
	echo "<input type=\"hidden\" name=\"type\" value=\"user\" />\n";
	echo "<input type=\"hidden\" name=\"username\" value=\"".$_REQUEST['username']."\" />\n";
	echo "<table class=\"searchList\">\n";
	
	$perms = $DESK->PermissionManager->UserPermissionList($_REQUEST['username']);
	
	$row=0;
	
	foreach($perms as $perm => $allowed)
	{
		// HTML-Safe
		$permhtml = str_replace(".","#",$perm);
	
		$class = "row".$row++;
		if ($row>1) $row=0;
		echo "<tr class=\"".$class."\">";
		
		echo "<td>\n";
		echo $perm;
		echo "</td>\n";
		
		echo "<td>\n";
		echo "<input type=\"radio\" name=\"".$permhtml."\" value=\"-1\"";
		if ($allowed == -1)
			echo " checked";
		echo " />";
		echo $DESK->Lang->Get("undefined");
		echo "</td>";
		
		echo "<td>\n";
		echo "<input type=\"radio\" name=\"".$permhtml."\" value=\"0\"";
		if ($allowed == 0)
			echo " checked";
		echo " />";
		echo $DESK->Lang->Get("denied");
		echo "</td>";
		
		echo "<td>\n";
		echo "<input type=\"radio\" name=\"".$permhtml."\" value=\"1\"";
		if ($allowed == 1)
			echo " checked";
		echo " />";
		echo $DESK->Lang->Get("allowed");
		echo "</td>";
		
		
		echo "</tr>";
	}
	
	echo "<tr><td> </td><td>\n";
	echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("save")."\" onclick=\"DESK.formAPI('permission_form');\" />\n";
	echo "</td></tr>";
	
	echo "</table>\n";
	echo "</form>\n";
	
	//echo memory_get_usage();
}
else if ($mode == "group")
{
	echo "<br />".sa_link("&lt;&lt; ".$DESK->Lang->Get("system_admin"))."<br />\n";
	echo "<h3>".$DESK->Lang->Get("admin_group")."</h3>\n";
	$groups = $DESK->PermissionManager->GroupList();
	
	foreach($groups as $id => $name)
	{
		echo "<form id=\"delete_group_".$id."\" onsubmit=\"return false;\" />\n";
		echo "<input type=\"hidden\" name=\"mode\" value=\"permgroup_delete\" />\n";
		echo "<input type=\"hidden\" name=\"permgroupid\" value=\"".$id."\" />\n";
		echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("delete")."\" onclick=\"if(confirm('Delete group ".$id."')) DESK.formAPI('delete_group_".$id."',false,false,DESK.refreshSubpage);\" />\n";
		echo " ";
		$opts = array("mode" => "groupedit", "permgroupid" => $id);
		echo sa_link($id.": ".$name,$opts);
		echo "\n</form>\n";
	}
}
else if ($mode == "groupedit")
{
	$groups = $DESK->PermissionManager->GroupList();
	echo "<br />".sa_link("&lt;&lt; ".$DESK->Lang->Get("system_admin"))." \n";
	echo sa_link("&lt;&lt; ".$DESK->Lang->Get("admin_group"),array("mode"=>"group"))."<br />\n";
	$group = $groups[$_REQUEST['permgroupid']];
	$id=$_REQUEST['permgroupid'];
	echo "<h3>".$id.": ".$group."</h3>\n";
	
	echo "<form id=\"permission_form\" onsubmit=\"return false;\">\n";
	echo "<input type=\"hidden\" name=\"mode\" value=\"permission_save\" />\n";
	echo "<input type=\"hidden\" name=\"type\" value=\"group\" />\n";
	echo "<input type=\"hidden\" name=\"groupid\" value=\"".$id."\" />\n";
	echo "<table class=\"searchList\">\n";
	
	$perms = $DESK->PermissionManager->GroupPermissionList($id);
	
	$row=0;
	
	foreach($perms as $perm => $allowed)
	{
		// HTML-Safe
		$permhtml = str_replace(".","#",$perm);
	
		$class = "row".$row++;
		if ($row>1) $row=0;
		echo "<tr class=\"".$class."\">";
		
		echo "<td>\n";
		echo $perm;
		echo "</td>\n";
		
		echo "<td>\n";
		echo "<input type=\"radio\" name=\"".$permhtml."\" value=\"-1\"";
		if ($allowed == -1)
			echo " checked";
		echo " />";
		echo $DESK->Lang->Get("undefined");
		echo "</td>";
		
		echo "<td>\n";
		echo "<input type=\"radio\" name=\"".$permhtml."\" value=\"0\"";
		if ($allowed == 0)
			echo " checked";
		echo " />";
		echo $DESK->Lang->Get("denied");
		echo "</td>";
		
		echo "<td>\n";
		echo "<input type=\"radio\" name=\"".$permhtml."\" value=\"1\"";
		if ($allowed == 1)
			echo " checked";
		echo " />";
		echo $DESK->Lang->Get("allowed");
		echo "</td>";
		
		
		echo "</tr>";
	}
	
	echo "<tr><td> </td><td>\n";
	echo "<input type=\"submit\" value=\"".$DESK->Lang->Get("save")."\" onclick=\"DESK.formAPI('permission_form');\" />\n";
	echo "</td></tr>";
	
	echo "</table>\n";
	echo "</form>\n";
}
else
{
	echo "<h3>".$DESK->Lang->Get("system_admin")."</h3>\n";
	echo ErrorCode::UnknownMode.": ".$DESK->Lang->Get("action_invalid")." (".$mode.")<br />\n";
	exit();
}
echo "</html>";
?>
