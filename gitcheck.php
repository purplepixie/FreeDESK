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

// What to exclude - the ./.git directory and also our release directory
$exclude = array(
	"./.git",
	"./release" );

$gitout = array();
exec("git ls-files", $gitout);

$gitfiles=array();
foreach($gitout as $gitfile)
	$gitfiles["./".$gitfile]=false;

$physfiles=array();

// Identify temp files from gEdit (~ on the end)
function isTempFile($file)
{
	if (substr($file,-1)=="~")
		return true;
	return false;
}

// Recursive function to check a directory
function checkDir($path)
{
	// File and exclude arrays
	global $physfiles,$gitfiles, $exclude;
	$handle = opendir($path); // open the directory
	while (false !== ($file = readdir($handle))) // iterate through
	{
		$filepath = $path.$file; // get a "full" filepath
		
		// Exclude . .. temp files and anything in our exclude array
		if ($file != "." && $file != ".." && 
			!isTempFile($file) && !in_array($filepath, $exclude) )
		{
			// If a directory recursively call checkDir again
			if (is_dir($filepath))
				checkDir($filepath."/");
			else // is a file
			{
				// Check it against the list of git files
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

// Show orphaned files - those in git but not on the filesystem
$orphancount=0;
foreach($gitfiles as $file => $physical)
{
	if (!$physical)
	{
		$orphancount++;
		echo "ORPHAN: ".$file."\n";
	}
}
// Show orphan summary
echo $orphancount." orphaned files (in git but not physically listed)\n";

// Show missing git files (exist on filesystem but not in git)
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
// Display a summary
echo "Total ".$filecount." physical files, ".$ingitcount." in git, ".$misscount." missing.\n";

?>
