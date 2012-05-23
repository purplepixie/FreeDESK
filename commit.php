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
 * Runs a commit to github with optional SQL inclusion
**/

function fdSQL($line)
{
	$line=str_replace("TABLE `","TABLE `%%$%%", $line);
	$line=str_replace("EXISTS `","EXISTS `%%$%%", $line);
	return $line;
}

function writeSQL($file, $contents, $fdsql=false)
{
	$fp = fopen($file, "w");
	if (is_array($contents))
	{
		foreach($contents as $line)
		{
			if ($fdsql)
				$line=fdSQL($line);
			fwrite($fp, $line."\n");
		}
	}
	else
	{
		if ($fdsql)
			$contents=fdSQL($contents);
		fwrite($fp, $contents);
	}
	fclose($fp);
}

$handle = fopen("php://stdin", "r");


echo "Commit Message: ";
$msg = fgets($handle,1024);

echo "Push to GITHUB (Y/n)? ";
$push = fgets($handle,1024);
if (strtoupper($push) != "N")
	$pushgit=true;
else
	$pushgit=false;


echo "SQL Dump (Y/n)? ";
$sql = fgets($handle,1024);
if (strtoupper($sql) != "N")
	$sqldump=true;
else
	$sqldump=false;
	
	
echo "Dropbox (Y/n)? ";
$dbox = fgets($handle,1024);
if (strtoupper($dbox) != "N")
	$dropbox=true;
else
	$dropbox=false;

if ($sqldump)
{
	$dbuser="freedesk";
	$dbpass="freedesk";
	$dbdata="freedesk";
	
	// First schema with drops
	$output="";
	$dbdrop=true;
	include("sqltools/dump.php");
	$schemadrop = $output;
	
	// Schema without drop
	$output="";
	$dbdrop=false;
	include("sqltools/dump.php");
	$schema = $output;
	
	// Finally myrug output
	$output="";
	include("sqltools/upgrade.php");
	$myrug = $output;
	
	// Now write the contents
	writeSQL("freedesk/sql/schema-drop.sql",$schemadrop);
	writeSQL("freedesk/sql/schema.sql",$schema);
	writeSQL("freedesk/sql/upgrade.sql",$myrug);
	// FDSQL Versions with tables prefixed by %%$%%
	writeSQL("freedesk/sql/schema-drop.fdsql",$schemadrop,true);
	writeSQL("freedesk/sql/schema.fdsql",$schema,true);
	writeSQL("freedesk/sql/upgrade.fdsql",$myrug,true);
}

system("git commit -a -m \"".$msg."\"");

if ($pushgit)
	system("git push -u origin master");

if ($dropbox)
{
	system("mkdir ~/Dropbox/FreeDESK");
	system("cp -Rf * ~/Dropbox/FreeDESK/");
}

?>
