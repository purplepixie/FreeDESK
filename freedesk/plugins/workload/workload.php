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
class workload extends FreeDESK_PIM
{
// The startup method - register functionalityh
function Start()
{
  // Register ourselves as a plugin
  $this->DESK->PluginManager->Register(new Plugin(
    "Workload", "0.01", "Reporting" ));
  // Register a page which will be displayed
  $this->DESK->PluginManager->RegisterPIMPage("workload_page",
    $this->ID);
  // Register an API call for details
  $this->DESK->PluginManager->RegisterPIMAPI("workload_api",
    $this->ID);
  // Add a permission for this action
  $this->DESK->PermissionManager->Register("workload", false);
  // Register a JS script (workload.js) in our directory for inclusion
  // Note the file can be called anything we like or even not in the
  // directory (unlike this PHP file)
  $jspath = $this->webpath . "workload.js";
  $this->DESK->PluginManager->RegisterScript($jspath);
  // Register a CSS file (workload.css)
  $csspath = $this->webpath . "workload.css";
  $this->DESK->PluginManager->RegisterCSS($csspath);
}
// The method to add menu items
function BuildMenu()
{
  // Check if the permission is allowed or don't show the menu
  if ($this->DESK->ContextManager->Permission("workload"))
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
    $sReport->tag="workload";
    $sReport->display="Workload";
    // Set the action for the click
    // (show the page 'workload' registered above)
    $sReport->onclick = "DESK.loadSubpage('workload_page');";
    // Add the menu option to the reporting menu
    $this->DESK->ContextManager->AddMenuItem("reporting",$sReport);
  }
}
// Handle page requests (specifically the workload page)
function Page($page)
{
  if ($page == "workload_page" // page is the workload page
  	&& // and permission for this page
  	$this->DESK->ContextManager->Permission("workload"))
  {
    // Page title
    echo "<h3>Workload</h3>\n";
    // Team and user list
    $teamuser = $this->DESK->RequestManager->TeamUserList();
    // Iterate through this list and display workload info
    echo "<table>\n";
    foreach($teamuser as $teamid => $teamdata)
    {
      // Counter for the team
      $teamcount=0;
      echo "<tr>\n";
      echo "<td>\n";
      echo "<b>".$teamdata['name']."</b>\n";
      echo "</td>\n";
      echo "<td><b>\n";
      // Get the requests assigned to just the team
      $requests = $this->DESK->RequestManager->FetchAssigned($teamid, "");
      $reqcount = sizeof($requests);
      echo $reqcount;
      // Increment team counter
      $teamcount += $reqcount;
      echo "</b></td>";
      // Detail link for the team
      $js="Workload.Detail(".$teamid.",'');";
      echo "<td>\n";
      echo "<a href=\"#\" onclick=\"".$js."\">Detail</a>\n";
      echo "</td>";
      echo "</tr>";
      // Detail row for the team
      echo "<tr><td colspan=\"3\" id=\"detail_".$teamid."_\"></td></tr>\n";
      // Iterate through the users in the team
      foreach($teamdata['items'] as $user => $userdata)
      {
        echo "<tr>\n";
        echo "<td>\n";
        echo $userdata['realname'];
        echo "</td>\n";
        echo "<td>\n";
        // Requests for this user and team
        $requests = $this->DESK->RequestManager->FetchAssigned($teamid, $user);
        $reqcount = sizeof($requests);
        echo $reqcount;
        // Increment team counter
        $teamcount+=$reqcount;
        echo "</td>\n";
        // Detail link for the team and user
        $js="Workload.Detail(".$teamid.",'".$user."');";
        echo "<td>\n";
        echo "<a href=\"#\" onclick=\"".$js."\">Detail</a>\n";
        echo "</td>";
        echo "</tr>\n";
        // Detail row for the user
        echo "<tr><td colspan=\"3\" id=\"detail_".$teamid."_".$user."\"></td></tr>\n";
      }
    // The team total
    echo "<tr><td><b>Total</b></td><td><b>".$teamcount."</b></td></tr>\n";
    // A spacer
    echo "<tr><td colspan=\"3\"><hr class=\"workload\"></td></tr>\n";
    }
    echo "</table>\n";
  }
}
// API Handler
function API($mode)
{
  if ($mode == "workload_api" // Correct API Mode
    && // and permission for workload
    $this->DESK->ContextManager->Permission("workload"))
  {
    // Get the team and user requested
    $teamid = isset($_REQUEST['teamid']) ? $_REQUEST['teamid'] : 0;
    $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : "";
    // Get the assigned requests
    $reqs = $this->DESK->RequestManager->FetchAssigned($teamid,$username);
    // Get the list of priorities
    $pris = $this->DESK->RequestManager->GetPriorityList();
    // Our output array with unknown default
    $out = array(0=>array("name"=>"Unknown","total"=>0));
    // Iterate through the requests
    foreach($reqs as $req)
    {
      // Get the priority
      $pri = $req->Get("priority");
      // Check if exists and add to totals
      if (isset($pris[$pri])) // valid
      {
        if (isset($out[$pri]))
          $out[$pri]['total']++;
        else
          $out[$pri]=array(
            "name"=>$pris[$pri]['priorityname'],
            "total"=>1 );
      }
      else
        $out[0]["total"]++;
    }
    // Build the HTML
    $html = "";
    // For each detail line
    foreach($out as $line)
    {
      $html.=$line["name"].": ".$line["total"]."<br />";
    }
    // Create the XML output
    // XML creation object
    $xml = new xmlCreate();
    // Add a char element with CDATA encoding
    $xml->charElement(
      "data",
      $html,
      0,
      false,
      true );
    // Output the XML with the header
    $xml->echoXML(true);
    // Exit (ensure no other output)
    exit();
  }
}
    

}
?>
