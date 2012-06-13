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

// Check git is up-to-date with all our files to be added using "git ls-files"
// compared against actual directory structure

$gitout = array();
exec("git ls-files", $gitout);

$gitfiles=array();
foreach($gitout as $gitfile)
	$gitfiles["./".$gitfile]=false;

$physfiles=array();

function checkDir($path)
{
	global $physfiles,$gitfiles;
	$handle = opendir($path);
	while (false !== ($file = readdir($handle)))
	{
		$filepath = $path.$file;
		if ($file != "." && $file != ".." && 
			$file != ".git" && (substr($file,-1)!="~") && 
			$filepath != "./release" )
		{
			if (is_dir($filepath))
				checkDir($filepath."/");
			else
			{
				if (isset($gitfiles[$filepath]))
					$ingit=true;
				else
					$ingit=false;
				$physfiles[$filepath]=$ingit;
				if ($ingit)
					$gitfiles[$filepath]=true;
			}
		}
	}
	closedir($handle);
}

checkDir("./");

$orphancount=0;
foreach($gitfiles as $file => $physical)
{
	if (!$physical)
	{
		$orphancount++;
		echo "ORPHAN: ".$file."\n";
	}
}
if ($orphancount>0)
{
	echo $orphancount." orphaned files (in git but not physically listed\n";
}
else
	echo "0 orphaned files found in git respository\n";

$filecount=0;
$ingitcount=0;
$misscount=0;
foreach($physfiles as $file => $ingit)
{
/*
	if ($ingit)
		echo " ";
	else
		echo "X";
	echo " ".$file."\n";
*/
	$filecount++;
	if (!$ingit)
	{
		$misscount++;
		echo "MISSING: ".$file."\n";
	}
	else
		$ingitcount++;
}

echo "Total ".$filecount." physical files, ".$ingitcount." in git, ".$misscount." missing.\n";

?>
