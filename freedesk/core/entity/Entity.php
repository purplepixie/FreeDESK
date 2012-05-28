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
 * The 'standard' entity class
**/
class Entity extends EntityBase
{

/**
 * Register ourselves with FreeDESK
 * @param mixed &$freeDESK Instance of FreeDESK
**/
static function Exec(&$desk)
{
	$desk->PluginManager->Register(new Plugin(
		"Standard Entity", "0.01", "Entity", "", "Entity" ));
}

/**
 * Constructor
 * @param mixed &$freeDESK Instance of FreeDESK
**/
function Entity(&$freeDESK)
{
	parent::EntityBase($freeDESK);
}



}
?>
