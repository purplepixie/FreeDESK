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

ob_start();
include("core/FreeDESK.php");
$DESK = new FreeDESK("./");
$DESK->Start();
ob_end_clean();

header("Content-type: text/xml");
header("Expires: Tue, 27 Jul 1997 01:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_REQUEST['mode']))
{
	$error = new FreeDESK_Error(ErrorCode::UnknownMode, "Unknown Mode");
	echo $error->XML(true);
	exit();
}

if ($_REQUEST['mode']=="login")
{
	//echo $_REQUEST['username'].$_REQUEST['password'];
	// TODO: Other Login Modes
	if ($DESK->ContextManager->Open(ContextType::User, "", $_REQUEST['username'], $_REQUEST['password']))
	{
		echo $DESK->ContextManager->Session->XML(true);
		exit();
	}
	else // Login failed
	{
		$error = new FreeDESK_Error(ErrorCode::FailedLogin, "Login Failed");
		echo $error->XML(true);
		exit();
	}
}
else if ($_REQUEST['mode']=="logout")
{
	if ($DESK->ContextManager->Open(ContextType::User, $_REQUEST['sid']))
		$DESK->ContextManager->Destroy();
	$xml = new xmlCreate();
	$xml->charElement("logout","1");
	echo $xml->getXML(true);
	exit();
}

if (!$DESK->ContextManager->Open(ContextType::User, $_REQUEST['sid']))
{
	$error = new FreeDESK_Error(ErrorCode::SessionExpired, "Session Expired");
	echo $error->XML(true);
	exit();
}

if ($_REQUEST['mode']=="requests_assigned")
{
	$team = isset($_REQUEST['teamid']) ? $_REQUEST['teamid'] : 0;
	$user = isset($_REQUEST['username']) ? $_REQUEST['username'] : "";
	$list = $DESK->RequestManager->FetchAssigned($team, $user);
	echo xmlCreate::getHeader()."\n";
	echo "<request-list>\n";
	foreach($list as $item)
	{
		echo $item->XML(false)."\n";
	}
	echo "</request-list>\n";
	exit();
}

if ($_REQUEST['mode']=="entity_search")
{
	$entity = $DESK->DataDictionary->GetTable($_REQUEST['entity']);
	
	if ($entity === false || !$entity->editable)
	{
		//
	}
	
	if (!$DESK->ContextManager->Permission("entity_view.".$_REQUEST['entity']))
	{
		//
	}

	// ENTITY MANAGER
	$q="SELECT * FROM ".$DESK->Database->Table($entity->entity);
	
	/*
	$wc="";
	
	foreach($entity->fields as $key => $field)
	{
		if ($field->searchable && isset($_REQUEST[$key]) && ($_REQUEST[$key]!=""))
		{
			if ($wc != "")
				$wc.=" AND ";
			// Char data %
			$wc.=$DESK->Database->Field($key)."=".$DESK->Database->SafeQuote($_REQUEST[$key]);
		}
	}
	*/
	
	$qb = new QueryBuilder();
	$fieldcount = 0;
	foreach($entity->fields as $key => $field)
	{
		if ($field->searchable && isset($_REQUEST[$key]) && ($_REQUEST[$key]!=""))
		{
			if ($fieldcount++ > 0)
				$qb->AddOperation(QueryType::opAND);
			// Char data %
			//$wc.=$DESK->Database->Field($key)."=".$DESK->Database->SafeQuote($_REQUEST[$key]);
			$qb->Add($key, QueryType::Equal, $DESK->Database->SafeQuote($_REQUEST[$key]));
		}
	}
	

	
	if (isset($_REQUEST['start']))
		$start=$_REQUEST['start'];
	else
		$start = 0;
	
	if (isset($_REQUEST['limit']))
		$limit=$_REQUEST['limit'];
	else
		$limit = 30;


	$wc = $DESK->Database->Clause($qb);
	
	if ($wc != "")
		$q.=" WHERE ".$wc;
	
	$meta = array(
		"start" => $start,
		"limit" => $limit );
	
	$r=$DESK->Database->Query($q);
	
	$meta["count"]=$DESK->Database->NumRows($r);
	
	if ($meta["count"]>$limit)
	{
		$q.=" LIMIT ".$DESK->Database->Safe($start).",".$DESK->Database->Safe($limit);
		$DESK->Database->Free($r);
		$r=$DESK->Database->Query($q);
	}
	
	$xml = new xmlCreate();
	$xml->startElement("search-results");
	$xml->startElement("meta");
	foreach($meta as $key => $val)
		$xml->charElement($key, $val);
	$keyfield="";
	foreach($entity->fields as $key => $field)
	{
		if ($field->keyfield)
			$keyfield=$field->field;
		$xml->startElement("field-data");
		$xml->charElement("id",$field->field);
		$xml->charElement("name",$field->name, 0, false, true);
		$xml->endElement("field-data");
	}
	$xml->charElement("keyfield",$keyfield);
	$xml->endElement("meta");
	
	while($row=$DESK->Database->FetchAssoc($r))
	{
		$xml->startElement("entity");
		foreach($row as $key => $val)
		{
			$xml->charElement("field", $val, array("id"=>$key), false, true);
		}
		$xml->endElement("entity");
	}
	$DESK->Database->Free($r);
	
	$xml->endElement("search-results");
	
	echo $xml->getXML(true);
	exit();
}
	



$error = new FreeDESK_Error(ErrorCode::UnknownMode, "Unknown Mode");
echo $error->XML(true);
exit();

?>
