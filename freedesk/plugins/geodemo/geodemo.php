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

// A new class for the PIM extending FreeDESK_PIM,
// named the same as the directory and filename
class geodemo extends FreeDESK_PIM
{
// The startup method - register functionalityh
function Start()
{
  
  $this->DESK->PluginManager->Register(new Plugin(
    "GeoDemo", "0.01", "Other" ));
 
  $this->DESK->PluginManager->RegisterPIMPage("geodemo_page", $this->ID);
  $this->DESK->PluginManager->RegisterPIMPage("geodemo_content", $this->ID);
  
  $this->DESK->PermissionManager->Register("geodemo", false);
  
  //$jspath = $this->webpath . "geodemo.js";
  //$this->DESK->PluginManager->RegisterScript($jspath);
 
  $table = new DD_Table();
  $table->name = "Locations";
  $table->entity = "vis_country";
  $table->editable = true;
  
	$f = new DD_Field();
	$f->name="Code";
	$f->field="country";
	$f->type=DD_FieldType::Char;
	$f->size=254;
	$f->searchable=true;
	$f->display=true;
	$table->Add($f);
	
	$f = new DD_Field();
	$f->name="Country";
	$f->field="country_desc";
	$f->type=DD_FieldType::Char;
	$f->size=254;
	$f->searchable=true;
	$f->display=true;
	$table->Add($f);
	
	$f = new DD_Field();
	$f->name="Latitude";
	$f->field="lat";
	$f->type=DD_FieldType::Char;
	$f->size=254;
	$f->searchable=false;
	$f->display=true;
	$table->Add($f);
	
	$f = new DD_Field();
	$f->name="Longitude";
	$f->field="long";
	$f->type=DD_FieldType::Char;
	$f->size=254;
	$f->searchable=false;
	$f->display=true;
	$table->Add($f);
	
	$this->DESK->DataDictionary->Add($table);
}
// The method to add menu items
function BuildMenu()
{
  // Check if the permission is allowed or don't show the menu
  if ($this->DESK->ContextManager->Permission("geodemo"))
  {
    // Check if the reporting menu already exists, add if not
    $currentItems = $this->DESK->ContextManager->MenuItems();
    if (!isset($currentItems['reporting']))
    {
      $repmenu = new MenuItem();
      $repmenu->tag = "reporting";
      $repmenu->display = "Reporting";
      $this->DESK->ContextManager->AddMenuItem
        ("reporting", $repmenu);
    }

    // Built the menu item for this plugin
    $sReport = new MenuItem();
    $sReport->tag="geodemo";
    $sReport->display="GeoDemo Display";
    // Set the action for the click
    
    $sReport->onclick = "DESK.loadSubpage('geodemo_page');";
    // Add the menu option to the reporting menu
    $this->DESK->ContextManager->AddMenuItem("reporting",$sReport);
  }
}
// Handle page requests (specifically the workload page)
function Page($page)
{
  if ($page == "geodemo_page" // page is the workload page
  	&& // and permission for this page
  	$this->DESK->ContextManager->Permission("geodemo"))
  {
    // Page title
    echo "<h3>GeoDemo</h3>\n";
    //echo $this->DESK->ContextManager->Session->sid."<br />\n";
    echo "<iframe width=\"860\" height=\"600\" frameborder=\"0\" src=\"page.php?page=geodemo_content&sid=".$this->DESK->ContextManager->Session->sid."\"></iframe>\n";
  }
  else if ($page == "geodemo_content" // page is the workload page
  	&& // and permission for this page
  	$this->DESK->ContextManager->Permission("geodemo"))
  {
  	require($this->filepath . "map.php");
  }
}
// API Handler
function API($mode)
{
 //
}
  
  
function Install()
{
	$sqlfile = $this->filepath . "geodemo.sql";
	$fp = fopen($sqlfile, "r");
	
	$q="";
	while (!feof($fp))
	{
		$line = fgets($fp, 1024);
		if (trim($line) != "")
			$q.=trim($line);
		if ($q[strlen($q)-1]==";")
		{
			$this->DESK->Database->Query($q);
			$q="";
		}
	}
}

function Uninstall()
{
	$sql = "DROP TABLE `vis_country`";
	$this->DESK->Database->Query($sql);
}

}
?>
