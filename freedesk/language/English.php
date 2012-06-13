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
}

}
?>
