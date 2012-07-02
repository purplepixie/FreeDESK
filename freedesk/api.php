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
	$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : "";
	$order = isset($_REQUEST['order']) && $_REQUEST['order']=="D" ? "DESC" : "ASC";
	$list = $DESK->RequestManager->FetchAssigned($team, $user, $sort, $order);
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
	
	
	$qb = new QueryBuilder();
	$fieldcount = 0;
	foreach($entity->fields as $key => $field)
	{
		if ($field->searchable && isset($_REQUEST[$key]) && ($_REQUEST[$key]!=""))
		{
			if ($fieldcount++ > 0)
				$qb->AddOperation(QueryType::opAND);
			
			if ( ($field->type==DD_FieldType::Char || $field->type==DD_FieldType::Text)  &&
					strpos($_REQUEST[$key], "%") !== true )
				$qb->Add($key, QueryType::Like, $DESK->Database->SafeQuote($_REQUEST[$key]));
			else
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
	
else if ($_REQUEST['mode'] == "entity_save")
{
	$entity = $_REQUEST['entity'];
	$table = $DESK->DataDictionary->GetTable($entity);
	
	if ($entity === false)
	{
		$err = new FreeDESK_Error(ErrorCode::EntityError, "Entity Error (Not Found)");
		echo $err->XML(true);
		exit();
	}
	
	$keyfield = $table->keyfield;
	
	$data = $DESK->EntityManager->Load($entity, $_REQUEST[$keyfield]);
	
	
	if ($data === false)
	{
		$err = new FreeDESK_Error(ErrorCode::EntityError, "Entity Error (Not Loaded)");
		echo $err->XML(true);
		exit();
	}
	
	foreach($table->fields as $id => $field)
	{
		if ($id != $keyfield)
			if (isset($_REQUEST[$id]))
				$data->Set($id, $_REQUEST[$id]);
	}
	
	$result = $DESK->EntityManager->Save($data);
	
	if ($result)
	{
		$xml = new xmlCreate();
		$xml->charElement("operation","1");
		echo $xml->getXML(true);
		exit();
	}
	else
	{
		$err = new FreeDESK_Error(ErrorCode::EntityError, "Entity Error (Not Saved)");
		echo $err->XML(true);
		exit();
	}
}

else if ($_REQUEST['mode'] == "entity_create")
{
	$entity = $_REQUEST['entity'];
	$table = $DESK->DataDictionary->GetTable($entity);
	
	if ($entity === false)
	{
		$err = new FreeDESK_Error(ErrorCode::EntityError, "Entity Error (Not Found)");
		echo $err->XML(true);
		exit();
	}
	
	$data = $DESK->EntityManager->Create($entity);
	
	foreach($table->fields as $id => $field)
	{
		if ($id != $keyfield)
			if (isset($_REQUEST[$id]))
				$data->Set($id, $_REQUEST[$id]);
	}
	
	$result = $DESK->EntityManager->Insert($data);
	
	if ($result)
	{
		$xml = new xmlCreate();
		$xml->charElement("operation","1");
		echo $xml->getXML(true);
		exit();
	}
	else
	{
		$err = new FreeDESK_Error(ErrorCode::EntityError, "Entity Error (Not Saved)");
		echo $err->XML(true);
		exit();
	}
}

else if ($_REQUEST['mode'] == "user_edit")
{
	if (!$DESK->ContextManager->Permission("user_admin"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$q = "UPDATE ".$DESK->Database->Table("user")." SET ";
	
	$q.=$DESK->Database->Field("username")."=".$DESK->Database->SafeQuote($_REQUEST['username']).",";
	$q.=$DESK->Database->Field("realname")."=".$DESK->Database->SafeQuote($_REQUEST['realname']).",";
	$q.=$DESK->Database->Field("email")."=".$DESK->Database->SafeQuote($_REQUEST['email']).",";
	$q.=$DESK->Database->Field("permgroup")."=".$DESK->Database->SafeQuote($_REQUEST['permgroup']);
	
	$q.=" WHERE ".$DESK->Database->Field("username")."=".$DESK->Database->SafeQuote($_REQUEST['original_username']);
	
	$DESK->Database->Query($q);
	
	if (isset($_REQUEST['password']) && $_REQUEST['password']!="")
	{
		$amb = new AuthMethodStandard($DESK);
		$amb->SetPassword($_REQUEST['username'], $_REQUEST['password']);
	}
	
	$q="DELETE FROM ".$DESK->Database->Table("teamuserlink")." WHERE ".$DESK->Database->Field("username")."="
		.$DESK->Database->SafeQuote($_REQUEST['original_username']);
	$DESK->Database->Query($q);
	
	if (isset($_REQUEST['team']))
	{
		foreach($_REQUEST['team'] as $team)
		{
			$q="INSERT INTO ".$DESK->Database->Table("teamuserlink")."(".$DESK->Database->Field("username").","
				.$DESK->Database->Field("teamid").") VALUES(".$DESK->Database->SafeQuote($_REQUEST['username']).","
				.$DESK->Database->Safe($team).")";
			$DESK->Database->Query($q);
		}
	}
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();
}

else if ($_REQUEST['mode'] == "request_update")
{
	// TODO: PERMISSIONS + PUBLIC
	
	$public=false;
	if (isset($_REQUEST['public']) && $_REQUEST['public']==1)
		$public=true;
	
	$req = $DESK->RequestManager->Fetch($_REQUEST['requestid']);
	if ($req === false)
	{
		$error = new FreeDESK_Error(ErrorCode::UnknownRequest, "Unknown Request");
		echo $error->XML(true);
		exit();
	}
	
	if (isset($_REQUEST['update']) && $_REQUEST['update']!="")
		$req->Update($_REQUEST['update'], $public);
	
	if (isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=" " && is_numeric($_REQUEST['status']))
		$req->Status($_REQUEST['status'], $public);
	
	// TODO: ASSIGNMENT PERMISSION
	if (isset($_REQUEST['assign']) && $_REQUEST['assign'] != "" && $_REQUEST['assign'] != " ") // Composite assignment
	{
		$team = 0;
		$user = "";
		
		$assign = $_REQUEST['assign'];
		
		if (is_numeric($assign)) // just a team
			$team = $assign;
		else
		{
			$parts = explode("/",$assign);
			$team = $parts[0];
			if (isset($parts[1]))
				$user=$parts[1];
		}
		
		$req->Assign($team, $user, $public);
	}
	
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();
}

else if ($_REQUEST['mode'] == 'request_create')
{
	if (isset($_REQUEST['class']))
		$class = $_REQUEST['class'];
	else
		$class = "";
	
	// Request of required class
	$req = $DESK->RequestManager->CreateById($class);
	
	// Assignment of request: TODO permissions for this!
	$team=0;
	$user="";
	
	if (isset($_REQUEST['assign']))
	{
		if (is_numeric($assign)) // just a team
			$team = $assign;
		else
		{
			$parts = explode("/",$assign);
			$team = $parts[0];
			if (isset($parts[1]))
				$user=$parts[1];
		}
	}
	
	$id = $req->Create($_REQUEST['customer'], $_REQUEST['update'], $class, $_REQUEST['status'], 
		$team, $user);
	
	$xml = new xmlCreate();
	$xml->charElement("request", $id);
	echo $xml->getXML(true);
	exit();
}

else if ($_REQUEST['mode'] == 'permission_save')
{
	if (!$DESK->ContextManager->Permission("user_admin"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	if ($_REQUEST['type'] == "user")
	{
		$type="user";
		$usergroupid=$_REQUEST['username'];
	}
	else if ($_REQUEST['type'] == "group")
	{
		$type="group";
		$usergroupid=$_REQUEST['groupid'];
	}
	else
	{
		$error = new FreeDESK_Error(ErrorCode::UnknownMode, "Unknown Mode ".$_REQUEST['mode']);
		echo $error->XML(true);
		exit();
	}
	
	$q="DELETE FROM ".$DESK->Database->Table("permissions")." WHERE ";
	$q.=$DESK->Database->Field("permissiontype")."=".$DESK->Database->SafeQuote($type)." AND ";
	$q.=$DESK->Database->Field("usergroupid")."=".$DESK->Database->SafeQuote($usergroupid);
	
	$DESK->Database->Query($q);
	
	$perms = $DESK->PermissionManager->PermissionList();
	
	foreach($perms as $perm => $def)
	{
		$htmlperm = str_replace(".","#",$perm);
		if (isset($_REQUEST[$htmlperm]))
		{
			$DESK->LoggingEngine->Log($perm,$_REQUEST[$htmlperm],"hi");
			$val=-1;
			if ($_REQUEST[$htmlperm] == "1")
				$val=1;
			else if ($_REQUEST[$htmlperm] == "0")
				$val=0;
			
			if ($val==1 || $val==0)
			{
				$q="INSERT INTO ".$DESK->Database->Table("permissions")."(";
				$q.=$DESK->Database->Field("permissiontype").",".$DESK->Database->Field("permission").",";
				$q.=$DESK->Database->Field("usergroupid").",".$DESK->Database->Field("allowed").") VALUES(";
				$q.=$DESK->Database->SafeQuote($type).",".$DESK->Database->SafeQuote($perm).",";
				$q.=$DESK->Database->SafeQuote($usergroupid).",".$DESK->Database->Safe($val).")";
				
				$DESK->Database->Query($q);
			}
		}
	}
	
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();
}

else if ($_REQUEST['mode'] == "create_user")
{
	if (!$DESK->ContextManager->Permission("user_admin"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}

	if ($_REQUEST['username'] != "")
	{
		$q="INSERT INTO ".$DESK->Database->Table("user")."(".$DESK->Database->Field("username").") VALUES(";
		$q.=$DESK->Database->SafeQuote($_REQUEST['username']).")";
		$DESK->Database->Query($q);
	}
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();	
}

else if ($_REQUEST['mode'] == "delete_user")
{
	// TODO: Delete related data e.g. perms, links and reassign requests
	if (!$DESK->ContextManager->Permission("user_admin"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}

	if ($_REQUEST['username'] != "")
	{
		$q="DELETE FROM ".$DESK->Database->Table("user")." WHERE ";
		$q.=$DESK->Database->Field("username")."=".$DESK->Database->SafeQuote($_REQUEST['username']);
		$DESK->Database->Query($q);
	}
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();	
}

else if ($_REQUEST['mode'] == "permgroup_delete")
{
	$id=$_REQUEST['permgroupid'];
	if (!$DESK->ContextManager->Permission("user_admin"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$DESK->PermissionManager->DeleteGroup($id);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();	
}

else if ($_REQUEST['mode'] == "permgroup_create")
{
	$name=$_REQUEST['groupname'];
	if (!$DESK->ContextManager->Permission("user_admin"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$DESK->PermissionManager->CreateGroup($name);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();	
}

else if ($_REQUEST['mode'] == "team_create")
{
	$name=$_REQUEST['teamname'];
	if (!$DESK->ContextManager->Permission("user_admin"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$DESK->RequestManager->CreateTeam($name);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();	
}

else if ($_REQUEST['mode'] == "team_update")
{
	$name=$_REQUEST['teamname'];
	$id=$_REQUEST['id'];
	if (!$DESK->ContextManager->Permission("user_admin"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$DESK->RequestManager->UpdateTeam($id,$name);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();	
}

else if ($_REQUEST['mode'] == "team_delete")
{
	$id=$_REQUEST['id'];
	if (!$DESK->ContextManager->Permission("user_admin"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$DESK->RequestManager->DeleteTeam($id);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();	
}

else if ($_REQUEST['mode'] == "status_create")
{
	$name=$_REQUEST['name'];
	if (!$DESK->ContextManager->Permission("user_admin"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$DESK->RequestManager->CreateStatus($name);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();	
}

else if ($_REQUEST['mode'] == "status_delete")
{
	$id=$_REQUEST['id'];
	if (!$DESK->ContextManager->Permission("user_admin"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$DESK->RequestManager->DeleteStatus($id);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();	
}

else if ($_REQUEST['mode'] == "status_update")
{
	$id=$_REQUEST['id'];
	$name=$_REQUEST['name'];
	if (!$DESK->ContextManager->Permission("user_admin"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$DESK->RequestManager->UpdateStatus($id,$name);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();	
}

else if ($_REQUEST['mode'] == "plugin_install")
{
	if (!$DESK->ContextManager->Permission("sysadmin_plugins"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$plugin=$_REQUEST['plugin'];
	
	$DESK->PluginManager->InstallPIM($plugin);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();
}

else if ($_REQUEST['mode'] == "plugin_activate")
{
	if (!$DESK->ContextManager->Permission("sysadmin_plugins"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$id=$_REQUEST['id'];
	
	$DESK->PluginManager->ActivatePIM($id, true);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();
}

else if ($_REQUEST['mode'] == "plugin_deactivate")
{
	if (!$DESK->ContextManager->Permission("sysadmin_plugins"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$id=$_REQUEST['id'];
	
	$DESK->PluginManager->ActivatePIM($id, false);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();
}

else if ($_REQUEST['mode'] == "plugin_uninstall")
{
	if (!$DESK->ContextManager->Permission("sysadmin_plugins"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$id=$_REQUEST['id'];
	
	$DESK->PluginManager->UninstallPIM($id);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();
}

else if ($_REQUEST['mode'] == "sysvar_save")
{
	if (!$DESK->ContextManager->Permission("sysadmin_advanced"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$id = $_REQUEST['id'];
	$value = $_REQUEST['value'];
	
	$DESK->Configuration->Set($id, $value, true);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();
}

else if ($_REQUEST['mode'] == "sysvar_create")
{
	if (!$DESK->ContextManager->Permission("sysadmin_advanced"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$id = $_REQUEST['id'];
	$value = $_REQUEST['value'];
	
	$DESK->Configuration->Set($id, $value, true);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();
}

else if ($_REQUEST['mode'] == "sysvar_delete")
{
	if (!$DESK->ContextManager->Permission("sysadmin_advanced"))
	{
		$error = new FreeDESK_Error(ErrorCode::Forbidden, "Permission Denied");
		echo $error->XML(true);
		exit();
	}
	
	$id = $_REQUEST['id'];
	$value = $_REQUEST['value'];
	
	$DESK->Configuration->Delete($id);
	
	$xml = new xmlCreate();
	$xml->charElement("operation","1");
	echo $xml->getXML(true);
	exit();
}


if ($DESK->PluginManager->API($_REQUEST['mode']))
	exit();

$error = new FreeDESK_Error(ErrorCode::UnknownMode, "Unknown Mode ".$_REQUEST['mode']);
echo $error->XML(true);
exit();

?>
