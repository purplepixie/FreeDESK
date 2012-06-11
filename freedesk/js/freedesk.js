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
 * The main FreeDESK JavaScript client-side code
**/

function FreeDESK()
{
	this.sid = ""; // Session ID
	
	this.requestStatus = new Array();


	// Login Support
	this.login_action = function(responseXML)
	{
		//alert(responseXML);
		//var txt = document.createTextNode(responseXML);
		//document.getElementById("login_content").appendChild(txt);
		if (DESK.isError(responseXML))
		{
			DESK.show_login(DESK.getError(responseXML));
		}
		else
		{
			var newsid = responseXML.getElementsByTagName("sid")[0].childNodes[0].nodeValue;
			if (DESK.sid == "") // no current session so reload to index
			{
				var loc = "./?sid="+newsid;
				window.location.href = loc;
			}
			else
			{
				hide_login();
				// Any other actions?
			}
		}
	}

	this.login_click=function()
	{
		var req = new ServerRequest();
		req.url = "api.php?mode=login&type=user&username="
			+document.getElementById("login_username").value
			+"&password="
			+document.getElementById("login_password").value;
		req.callback = DESK.login_action;
		req.Get();
	}
	
	this.show_login=function(errormsg)
	{
		this.backdrop(true);
		var txt = "";
		if (errormsg !== undefined)
			txt=errormsg;
		document.getElementById("login_message").innerHTML = txt;
		document.getElementById("login_form").style.display = "block";
	}
	
	this.hide_login=function()
	{
		document.getElementById("login_form").style.display = "none";
		document.getElementById("login_message").style.display = "none";
		this.backdrop(false);
	}

	// Show/Hide Backdrop
	this.backdrop = function(set)
	{
		var bd = document.getElementById("screen_backdrop");
		if (set === undefined) // toggle
		{
			if (bd.style.display == "block")
				set = false;
			else
				set = true;
		}
		
		if (set)
			bd.style.display = "block";
		else
			bd.style.display = "none";
	}
	
	// Check for errors
	this.isError = function(xml)
	{
		//alert(xml);
		//alert(xml.documentElement);
		//alert(xml.documentElement.tagName);
		//alert(xml.getElementsByTagName("error")[0].childNodes[0].nodeValue);
		if (xml.documentElement.tagName == "error")
		//if (xml.getElementsByTagName("error").length > 0)
			return true;
		return false;
	}
	
	this.getError = function(xml)
	{
		var out = xml.getElementsByTagName("code")[0].childNodes[0].nodeValue;
		out += ": ";
		out += xml.getElementsByTagName("text")[0].childNodes[0].nodeValue;
		return out;
	}
	
	// Display main or sub page (true for main, false for sub)
	this.displayMain = function(disp)
	{
		if (disp)
		{
			document.getElementById("subpage").style.display="none";
			document.getElementById("mainpage").style.display="block";
		}
		else
		{
			document.getElementById("mainpage").style.display="none";
			document.getElementById("subpage").style.display="block";
		}
	}
	
	// Load sub-pages
	this.displaySubpage = function(text)
	{
		document.getElementById("subpage").innerHTML = text;
		DESK.displayMain(false);
	}
	
	this.loadSubpage = function(page)
	{
		var sr = new ServerRequest();
		sr.xmlrequest=false;
		sr.url = "page.php?page="+page+"&sid="+this.sid;
		sr.callback = DESK.displaySubpage;
		sr.Get();
	}
	
	// Load a Request List to the Main Pane
	this.mainPane = function(teamid, username)
	{
		alert(teamid+" "+username);
	}
		
	
}

var DESK = new FreeDESK();

