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

// The PIM Function
function workloadPIM()
{
  // Last detail items asked for
  var teamid = 0;
  var username = "";
  
  // Get detail for the given team and user
  this.Detail = function(teamid, username)
  {
    // Set last requested items
    this.teamid = teamid;
    this.username = username;
    
    // Create a ServerRequest object
    var sr = new ServerRequest();
    // Build the URL to the API
    var url = "api.php?";
    // Add the mode
    url += "mode=workload_api";
    // And the team and user detail
    url += "&teamid="+teamid+"&username="+username;
    // And the SID from FreeDESK
    url += "&sid="+DESK.sid;
    // Define the callback
    sr.callback = Workload.Callback;
    // Set the URL
    sr.url = url;
    // Set to XML
    sr.xmlrequest = true;
    // Call the API
    sr.Post();
  }
  
  // The callback method (API response)
  this.Callback = function(xml, additional)
  {
    // Check if the API has returned an error
    if (DESK.isError(xml))
    {
      // Display error
      Alerts.add(DESK.getError(xml), 2, 10);
      // Exit
      return;
    } // no error so continue
    // Strip the response from the server
    var data = xml.getElementsByTagName("data")[0]
      .firstChild.nodeValue;
    // Get the element to display it in
    var eleID = 'detail_'
      +Workload.teamid
      +"_"+Workload.username;
    var ele = document.getElementById(eleID);
    // And display the data
    ele.innerHTML = data;
  }
}
// Instantiate the workloadPIM object
var Workload = new workloadPIM();

