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
 * The main setup file
**/
?>
<!DOCTYPE html>
<head>
<title>FreeDESK Setup</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"skin/default/css/main.css\" />
<style type="text/css">
span.previous
{
	font-size: 16pt;
	font-weight: bold;
	color: #90e090;
	padding: 1em;
}
span.current
{
	font-size: 16pt;
	font-weight: bold;
	color: black;
	padding: 1em;
}
span.tocome
{
	font-size: 16pt;
	font-weight: bold;
	color: #909090;
	padding: 1em;
}
</style>
</head>
<body>
<?php
$setup=$_SERVER['SCRIPT_NAME'];
function step_list($steps, $current)
{
	echo "<div id=\"stepsdiv\">\n";
	for ($a=1; $a<=$steps; ++$a)
	{
		if ($a<$current)
			echo "<span class=\"previous\">";
		else if ($a==$current)
			echo "<span class=\"current\">";
		else
			echo "<span class=\"tocome\">";
		echo $a;
		echo "</span>";
	}
	echo "\n</div>\n";
}

function fdsql($file, $prefix, &$sql)
{
	$fp=@fopen("sql/".$file,"r");
	if (!$fp)
	{
		echo "Error opening ".$file."<br /><br />";
		return false;
	}
	$q="";
	while ($str = fgets($fp, 1024))
	{
		if ($str[0]!="-")
		{
			for ($a=0; $a<strlen($str); ++$a)
			{
				$chr = $str[$a];
				if ($chr==";")
				{
					$q=str_replace("%%$%%", $prefix, $q);
					mysql_query($q, $sql);
					$q="";
					$a=strlen($str)+10;
				}
				else
					$q.=$chr;
			}
		}
	}
}

if (!isset($_REQUEST['step']))
	$step=1;
else
	$step=$_REQUEST['step'];
if (!is_numeric($step))
	$step=1;

step_list(10, $step);
?>
<h2>FreeDESK Setup Process</h2>
<?php
if ($step == 1) // First page
{
?>
<h3>Initial Setup</h3>
This process will take you through a fresh install or upgrade of the FreeDESK system.<br /><br />
<b>If you are setup but are seeing this message then you need to delete the setup.php file in the freedesk directory</b><br /><br />
<?php
if (file_exists("config/Config.php"))
{
	echo "Configuration Already Exists - <a href=\"".$setup."?step=4\">Keep Existing Database Connection Settings</a><br /><br />";
}
echo "<a href=\"".$setup."?step=2\">Create New Database Connection Settings</a>";
}
else if ($step == 2) // Database Connection Settings
{
?>
<h3>Database Connection Settings</h3>
The following settings are for a connection to a MySQL database where the FreeDESK data will be stored.<br /><br />
<?php
echo "<form action=\"".$setup."\" method=\"post\">\n";
?>
<input type="hidden" name="step" value="3">
<table border="0">

<tr><td>Server</td>
<td><input type="text" name="db_server" value="localhost"></td></tr>

<tr><td>Username</td>
<td><input type="text" name="db_username" value=""></td></tr>
<tr><td>Password</td>
<td><input type="text" name="db_password" value=""></td></tr>

<tr><td>Table Prefix</td>
<td><input type="text" name="db_prefix" value="fd_"></td></tr>

<tr><td>Database</td>
<td><input type="text" name="db_database" value="freedesk"></td></tr>

<tr><td>Create Database</td>
<td><input type="checkbox" name="db_create" value="1" checked></td></tr>

<tr><td>Password Salt</td>
<td><input type="text" name="pwd_salt" value=""> e.g. three or four random characters</td></tr>

</table>
<input type="submit" value="Validate Settings and Create Configuration">
</form>
<?php

$fp=@fopen("config/Config.php","a");
if (!$fp)
{
	echo "Warning: the config/Config.php file is not writeable so you will have to manually update the file.<br /><br />";
	echo "Or if you make the file world-writeable (e.g. chmod 777 config in Linux) then the script will create it automatically.";
}
else
	fclose($fp);

}

else if ($step == 3) // Validate and Write
{
echo "<h3>Validating Connection Settings</h3>";
$sql=mysql_connect($_REQUEST['db_server'],$_REQUEST['db_username'],$_REQUEST['db_password']);
if (!$sql)
{
	echo "Error: Could not connect to the database. Please go back in your browser and input correct credentials<br /><br />";
	echo "</body></html>";
	exit();
}
echo "Successful connection to database server<br /><br />";

if (isset($_REQUEST['db_create']) && ($_REQUEST['db_create']=="1"))
{
	echo "Creating Database...<br /><br />";
	$q="CREATE DATABASE ".$_REQUEST['db_database'];
	mysql_query($q);
}

if (mysql_select_db($_REQUEST['db_database']))
{
	echo "Connected to database ".$_REQUEST['db_database']."<br /><br />";
}
else
{
	echo "Error: Could not select database ".$_REQUEST['db_database'].", go back on your browser and input correct database<br /><br />";
	echo "</body></html>";
	exit();
}

echo "<h3>Creating Config File</h3>";
$config = "<?php // Config.php created by setup.php for FreeDESK\n";
$config.="\nclass FreeDESK_Configuration\n{\n";
$config.="var \$db_System = \"MySQL\";\n";
$config.="var \$db_Server = \"".$_REQUEST['db_server']."\";\n";
$config.="var \$db_Username = \"".$_REQUEST['db_username']."\";\n";
$config.="var \$db_Password = \"".$_REQUEST['db_password']."\";\n";
$config.="var \$db_Database = \"".$_REQUEST['db_database']."\";\n";
$config.="var \$db_Prefix = \"".$_REQUEST['db_prefix']."\";\n";
$config.="var \$pwd_Hash = \"".$_REQUEST['pwd_salt']."\";\n";
$config.="}\n?>";

$fp = @fopen("config/Config.php","w");
if ($fp)
{
	echo "Writing config/Config.php...<br /><br />";
	fputs($fp, $config);
	fclose($fp);
}
else
{
	echo "Sorry cannot write to config/Config.php file.<br /><br />";
	echo "Either make the file writeable by the web server and then refresh this page or...<br /><br />";
	echo "Copy and paste the following into a new file and upload it as config/Config.php within the freedesk directory.<br /><br />";
	echo "<textarea cols=\"80\" rows=\"10\" readonly=\"readonly\">".$config."</textarea><br /><br />\n";
}
echo "<a href=\"".$setup."?step=4\">Continue With Installation</a>";

}


else if ($step == 4)
{
echo "<h3>Database Schema Setup/Upgrade</h3>";
echo "Select a mode for the schema setup<br /><br />";
echo "<a href=\"".$setup."?step=5&mode=fresh\">Perform a fresh install (any existing data will be lost or overriten)</a> - SELECT FOR NEW INSTALL OR TO START FROM SCRATCH<br /><br />";
echo "<a href=\"".$setup."?step=5&mode=upgrade\">Perform an update install (no existing data will be lost)</a> - SELECT TO UPDATE AN EXISTING SYSTEM<br /><br />";
}

else if ($step == 5)
{
require("config/Config.php");
echo "<h3>Import or Setup Schema</h3>";
$fdc = new FreeDESK_Configuration();

$sql = mysql_connect($fdc->db_Server, $fdc->db_Username, $fdc->db_Password)
	or die("Cannot connect to database");
mysql_select_db($fdc->db_Database)
	or die("Cannot select database");

if (isset($_REQUEST['mode']) && ($_REQUEST['mode']=="fresh"))
{
	fdsql("schema-drop.fdsql", $fdc->db_Prefix, $sql);
	fdsql("default.fdsql", $fdc->db_Prefix, $sql);
}
else
{
	fdsql("schema.fdsql", $fdc->db_Prefix, $sql);
	fdsql("upgrade.fdsql", $fdc->db_Prefix, $sql);
	fdsql("default.fdsql", $fdc->db_Prefix, $sql);
}
echo "Database Schema setup - <a href=\"".$setup."?step=6\">click here to continue</a><br /><br />\n";
}

else if ($step == 6)
{
echo "<h3>Final Stages</h3>";
echo "Trying to start FreeDESK...<br /><br />";
require("core/FreeDESK.php");
$DESK = new FreeDESK("./");
if ($DESK->Start())
{
	echo "FreeDESK started!<br /><br />";
	echo "<h3>Set Admin Password</h3>";
	echo "Set a password for the admin user - this is essential if this is a new install, fresh schema update or the password salt has changed.<br /><br />";
	echo "If you are using an existing setup and no connection data has changed just leave it blank to not set and just click go.<br /><br />";
	echo "<form action=\"".$setup."\" method=\"post\"><input type=\"hidden\" name=\"step\" value=\"7\">";
	echo "Password: <input type=\"text\" name=\"admin_password\" value=\"\"> ";
	echo "<input type=\"submit\" value=\"Go\"></form>";
}
else
{
	echo "Sorry there was a problem starting FreeDESK<br /><br />";
}

$DESK->Stop();
}

else if ($step == 7)
{
require("core/FreeDESK.php");
$DESK = new FreeDESK("./");
$DESK->Start();

if (isset($_REQUEST['admin_password']) && ($_REQUEST['admin_password'] != ""))
{
	echo "Setting admin password... ";
	$amb=new AuthMethodStandard($DESK);
	$amb->SetPassword("admin",$_REQUEST['admin_password']);
	echo "Done<br /><br />";
}

echo "Your setup is now complete and can be further configured from within the system.<br /><br />";
echo "<b>PLEASE NOW DELETE THIS FILE (freedesk/setup.php) OR YOU WILL BE UNABLE TO USE FREEDESK.</b><br /><br />";
echo "<a href=\"./\">Click here to login to FreeDESK</a><br /><br />";

$DESK->Stop();
}
else
{
echo "<h3>Unknown Mode</h3>";
}
?>

</body>
</html>
