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
 * FreeDESK English Class - this is the base set of language elements
**/
class FDL_English
{
/**
 * Load Language Elements
 * @param array &$i Items array (reference)
**/
static function English(&$i)
{
$i['welcome'] = "Welcome to FreeDESK";
$i['login'] = "Login to FreeDESK";
$i['login_cancel'] = "Cancel Login";
$i['username'] = "Username";
$i['password'] = "Password";
$i['login_fail'] = "Login Failed";
$i['login_invalid'] = "Invalid or Expired Login Details";

$i['select_portal'] = "Select your interface from the following";
$i['select_analyst'] = "I am an analyst";
$i['select_customer'] = "I am a customer";

$i['permission_denied'] = "Sorry you do not have permission for this action";
$i['entity_not_found'] = "Sorry this entity was not found";

$i['action_invalid'] = "Sorry but this action is invalid";

$i['search'] = "Search";
$i['save'] = "Save";
$i['save_close'] = "Save and Close";
$i['create'] = "Create";

$i['not_found'] = "Not Found";

$i['system_admin'] = "System Administration";
$i['admin_user'] = "User Administration";
$i['admin_group'] = "Security Groups";

$i['realname'] = "Real Name";
$i['email'] = "Email";
$i['team_membership'] = "Team Membership";
$i['permgroup'] = "Permission Group";

$i['assign'] = "Assign";
$i['no_change'] = "No Change";
$i['assigned_to'] = "Assigned to";

$i['unassigned'] = "Unassigned";

$i['customer'] = "Customer";
$i['request'] = "Request";

$i['permissions'] = "Permissions";
$i['undefined'] = "Undefined";
$i['denied'] = "Denied";
$i['allowed'] = "Allowed";

$i['delete'] = "Delete";

$i['user_create'] = "Create User";

$i['teams'] = "Teams";
$i['request_status'] = "Request Status";

$i['plugin_manager'] = "Plugin Manager";
$i['activate'] = "Activate";
$i['deactivate'] = "Deactivate";
$i['install'] = "Install";
$i['uninstall'] = "Uninstall";

$i['status'] = "Status";
$i['system_vars'] = "System Variables (Advanced)";

$i['request_class'] = "Request Classes";
$i['request_priority'] = "Request Priority";
$i['priority'] = "Priority";

$i['unknown'] = "Unknown";

$i['email_accounts'] = "Email Accounts";
$i['email_templates'] = "Email Templates";

$i['edit'] = "Edit";

$i['name'] = "Name";
$i['host'] = "Host";
$i['hostdesc'] = "SMTP Hostname (or blank for local)";
$i['email'] = "Email Address";
$i['from'] = "From Address";
$i['fromname'] = "From Name";
$i['wordwrap'] = "Word Wrap";
$i['auth'] = "Use Authentication";
$i['username'] = "Username";
$i['password'] = "Password";
$i['smtpsec'] = "SMTP Security Mode";

$i['yes'] = "Yes";
$i['no'] = "No";
$i['none'] = "None";
$i['test'] = "Test";

$i['template_open'] = "Open Request Template";
$i['template_close'] = "Close Request Template";
$i['template_update'] = "Update Request Template";
$i['available_macro'] = "Available Macros";

$i['email_customer'] = "Email Customer";

$i['to'] = "To";
$i['subject'] = "Subject";
$i['send'] = "Send";
}

}
?>
