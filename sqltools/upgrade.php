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

// Update tool based on myrug (www.purplepixie.org/myrug) - goes to $output

global $dbuser, $dbpass, $dbdata, $output, $tablefilter;
if (!isset($tablefilter)) $tablefilter="";



if ($tablefilter!="")
 $filter=" LIKE \"".mysql_escape_string($tablefilter)."%\"";
else
 $filter="";

$sql=mysql_connect("127.0.0.1", $dbuser, $dbpass)
 or die("Couldn't connect to MySQL");
mysql_select_db($dbdata)
 or die("Couldn't select database");

function c($t="")
{
global $output;
$output.= "-- ".$t."\n";
}

c("FreeDESK SQL Upgrade Script -- PurplePixie Systems/David Cutting");
c();

$q="SHOW TABLES".$filter;
$output.= "-- ".$q."\n";
$r=mysql_query($q);
while ($row=mysql_fetch_array($r))
	{
	$table=$row[0];
	$output.= "-- Table: ".$table."\n";

	//$output.= "DROP INDEX FROM ".$table."\n";
	
	$tq="DESCRIBE ".$table;
	c($tq);
	$tr=mysql_query($tq);
	while ($trow=mysql_fetch_array($tr))
		{
		// Field Type Null Key Default Extra		

		$f="ALTER TABLE `".$table."` CHANGE `".$trow['Field']."` `".$trow['Field']."` ".$trow['Type'];
		if (($trow['Null']=="")||($trow['Null']=="NO")) $f.=" NOT NULL";
		if ($trow['Extra']!="") $f.=" ".$trow['Extra'];
		if ($trow['Default']!="") $f.=" DEFAULT '".$trow['Default']."'";
		$output.= $f.";\n";

		$f="ALTER TABLE `".$table."` ADD `".$trow['Field']."` ".$trow['Type'];
		if (($trow['Null']=="")||($trow['Null']=="NO")) $f.=" NOT NULL";
		if ($trow['Extra']!="") $f.=" ".$trow['Extra'];
		if ($trow['Default']!="") 
			{
			/*
			$typarr=explode("(",$trow['Type']);
			$type=$typarr[0];
			$quot=true;
			switch($type)
				{
				case "TINYINT": case "SMALLINT": case "MEDIUMINT": case "INT": case "INTEGER": case "BIGINT":
				case "FLOAT": case "DOUBLE":
				$quot=false;
				break;
				}
			*/
			$f.=" DEFAULT '".$trow['Default']."'";
			}

		$output.= $f.";\n";

		if ($trow['Key']!="")
			{
			if ($trow['Key']=="PRI")
				$output.= "ALTER TABLE `".$table."` ADD PRIMARY KEY( `".$trow['Field']."` );\n";
			else if ($trow['Key']=="MUL")
				{
				// the one at a time way
				//$output.= "ALTER TABLE `".$table."` DROP INDEX `".$trow['Field']."` ;\n";
				//$output.= "ALTER TABLE `".$table."` ADD INDEX ( `".$trow['Field']."` );\n";
				$output.= "CREATE INDEX `".$trow['Field']."` ON `".$table."` ( `".$trow['Field']."` );\n";
				}
			else
				c("Unknown Key Type ".$trow['Key']);
			//else if ($trow['Key']=="MUL")
				// $output.= "ALTER TABLE `".$table."` ADD INDEX ( `".$trow['Field']."` );\n";
			}
		}
	mysql_free_result($tr);
	c();
	}
mysql_close($sql);
?>
