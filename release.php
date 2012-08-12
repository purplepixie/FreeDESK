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
 * Builds a release and pushes to purplepixie.org
**/

$url = "http://freedesk.purplepixie.org/";
$handle = fopen("php://stdin", "r");

ob_start();
require("freedesk/core/FreeDESK.php");
$DESK=new FreeDESK("freedesk/");
$v=$DESK->FullVersion();
ob_end_clean();

echo "Version: ".$v."\n";;

echo "Run Commit (Y/n)? ";
$commit = fgets($handle,1024);
if (strtoupper($commit[0]) == "N")
	$commit=false;
else
	$commit=true;

if ($commit)
	require("commit.php");

// Build the release
system("mkdir release/freedesk-".$v);
system("cp -Rf freedesk/* release/freedesk-".$v."/");
system("mv release/freedesk-".$v."/setup-dev.php release/freedesk-".$v."/setup.php");
system("rm release/freedesk-".$v."/config/Config.php");
system("rm `find release/freedesk-".$v."/ -name '*~'`");

echo "Compress and Release (Y/n)? ";
$rel = fgets($handle,1024);
if (strtoupper($rel[0]) == "N")
	exit();

chdir("release");
system("tar -c freedesk-".$v." > freedesk-".$v.".tar");
system("gzip freedesk-".$v.".tar");
system("rm -Rf freedesk-".$v);
chdir("../");

echo "Release (Y/n)? ";
$rel = fgets($handle,1024);
if (strtoupper($rel[0]) == "N")
	exit();

echo "Passcode: ";
$passcode = fgets($handle,1024);
$passcode = substr($passcode, 0, strlen($passcode)-1);


$fp=fopen($url."admin/release.php?mode=check&passcode=".$passcode."&version=".$v, "r");
if (!$fp)
{
	echo "Cannot open URL\n";
	exit();
}
$resp = fgets($fp, 1024);
fclose($fp);

if ($resp == "0")
{
	echo "Version does not already exist\n";
}
else if ($resp == "1")
{
	echo "Version already exists\n";
	exit();
}
else
{
	echo "Response to Check: ".$resp."\n";
	exit();
}



echo "Change Log (blank line to end):\n";
$changelog="";
$input = fgets($handle,1024);
while ($input != "\n")
{
	$changelog .= $input;
	$input = fgets($handle,1024);
}

if (file_exists("freedesk/RELEASE"))
	$release = file_get_contents("freedesk/RELEASE");
else
	$release="";

echo "Current Release (y/N)? ";
$rel = fgets($handle,1024);
if (strtoupper($rel[0]) == "Y")
	$currel=1;
else
	$currel=0;

echo "Current Development (Y/n)? ";
$dev = fgets($handle,1024);
if (strtoupper($dev[0]) == "N")
	$curdev=0;
else
	$curdev=1;

$md5=md5_file("release/freedesk-".$v.".tar.gz");

$post_data = array(
	"passcode" => $passcode,
	"version" => $v,
	"releasenotes" => $release,
	"md5" => $md5,
	"changelog" => $changelog,
	"mode" => "release",
	"file" => "@release/freedesk-".$v.".tar.gz"
	);
	
if ($currel==1)
	$post_data['current_release']=1;
if ($curdev==1)
	$post_data['current_development']=1;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url."admin/release.php");
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);

$response=curl_exec($ch);

echo $response;

?>
