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
	
	// Statuses of requests (text)
	this.requestStatus = new Array();
	// Priorities of requests (text)
	this.requestPriority = new Array();
	// List of display fields for request list
	this.fieldList = new Array();

	// XML of last request list fetched (for redisplay)
	this.lastListXML = null;
	
	// Last Request Details
	this.lastTeam = 0;
	this.lastUser = "";
	
	// Last subpage
	this.lastSubpage = "";
	this.lastSubpageOpts = "";
	
	// Sort Criteria
	this.sortField = "requestid";
	this.sortOrder = "D";
	
	// Refresh Event
	this.refreshEvent = null;

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
				//var loc = "./?sid="+newsid;
				//window.location.href = loc;
				document.forms['login_sid_form'].elements['sid'].value = newsid;
				document.forms['login_sid_form'].submit();
			}
			else
			{
				DESK.sid = newsid;
				DESK.hide_login();
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

	// Logout
	this.logout_click=function()
	{
		var req = new ServerRequest();
		req.url="api.php?mode=logout&sid="+this.sid;
		req.callback = DESK.logout_action;
		req.Get();
	}
	
	this.logout_action=function()
	{
		window.location.href="./";
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
	
	this.getErrorCode = function(xml)
	{
		return xml.getElementsByTagName("code")[0].childNodes[0].nodeValue;
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
	
	this.loadSubpage = function(page, opts)
	{
		if (opts == undefined)
			var opts = "";
		this.lastSubpage = page;
		this.lastSubpageOpts = opts;
		var sr = new ServerRequest();
		sr.xmlrequest=false;
		sr.url = "page.php?page="+page;
		if (opts != "")
			sr.url += "&"+opts;
		sr.url += "&sid="+this.sid;
		
		sr.callback = DESK.displaySubpage;
		sr.Get();
	}
	
	// Refresh the subpage
	this.refreshSubpage = function()
	{
		DESK.loadSubpage(DESK.lastSubpage, DESK.lastSubpageOpts);
	}
	
	// Load a Request List to the Main Pane
	this.mainPane = function(teamid, username)
	{
		if (teamid == undefined)
			var teamid = 0;
		if (username == undefined)
			var username="";
		//alert(teamid+" "+username);
		var sr = new ServerRequest();
		this.lastTeam = teamid;
		this.lastUser = username;
		
		sr.url = "api.php?mode=requests_assigned&teamid="+teamid+"&username="+username;
		if (this.sortField != "")
		{
			sr.url += "&sort="+this.sortField;
			sr.url += "&order="+this.sortOrder;
		}
		sr.url += "&sid="+this.sid;
		sr.callback = DESK.mainPaneDisplay;
		sr.Get();
	}
	
	// Refresh the Main Pane
	this.mainPaneRefresh = function()
	{
		DESK.mainPane(DESK.lastTeam, DESK.lastUser);
	}
	
	// Display a request list in the main pane
	this.mainPaneDisplay = function(xml)
	{
		DESK.lastListXML = xml;
		var table = document.createElement("table"); // table for results
		table.border=0;
		table.width="100%";
		table.className="requestList";
		
		var container = document.getElementById('mainright');
		
		if (container.hasChildNodes())
		{
			while(container.childNodes.length >= 1)
				container.removeChild(container.firstChild);
		}
		
		var requests = xml.getElementsByTagName("request");
		
		if (requests.length <= 0)
		{
			container.innerHTML = "<h3>No Requests Found</h3>";
			return;
		}
		
		container.appendChild(table);
		
		var title = table.insertRow(0);
		title.className = "requestListTitle";
		for (var i=0; i<DESK.fieldList.length; ++i)
		{
			if (DESK.fieldList[i][1] == 1) // displayed field
			{
				var cell = title.insertCell(-1);
				
				var fieldTitle = DESK.fieldList[i][0];
				
				var link = "<a href=\"#\" onclick=\"DESK.mainPaneSort('"+DESK.fieldList[i][2]+"');\">";
				link += fieldTitle;
				
				if (DESK.sortField == DESK.fieldList[i][2])
					if (DESK.sortOrder == "D")
						link+=" &gt;";
					else
						link+=" &lt;";
						
				link+"</a>";
				
				cell.innerHTML = link;
			}
		}
		
		for (var req=0; req<requests.length; ++req)
		{
			var request = requests[req];
			var row = table.insertRow(table.getElementsByTagName("tr").length);
			row.className="requestList";
			
			for (var fc=0; fc<DESK.fieldList.length; ++fc)
			{
				var field = DESK.fieldList[fc];
				if (field[1] == 1) // display this field
				{
					var contents = "";
					var data = request.getElementsByTagName(field[2])[0];
					if (!data) // no field data of this form returned
					{
						contents="&nbsp;-";
					}
					else
					{
						contents = (data.textContent == undefined) ? data.firstChild.nodeValue : data.textContent;
						if (field[2]=="status")
							contents = DESK.requestStatus[contents];
						else if (field[2]=="priority")
							contents = DESK.requestPriority[contents];
						else if (field[2]=="requestid")
						{
							var id = contents;
							contents = "<a href=\"#\" onclick=\"DESK.displayRequest("+contents+");\">"+contents+"</a>";
							// row.onclick = function(){ return DESK.displayRequest(id); }; // Always uses last - TODO fix it
						}
					}
					
					var cell = row.insertCell(-1);
					cell.innerHTML = contents;
				}
			}
		}
	}
	
	// Set main pane sort
	this.mainPaneSort = function(field)
	{
		if (DESK.sortField == field)
		{
			if (DESK.sortOrder == "D")
				DESK.sortOrder = "A";
			else
				DESK.sortOrder = "D";
		}
		else
		{
			DESK.sortField = field;
			DESK.sortOrder = "D";
		}
		DESK.mainPane(DESK.lastTeam, DESK.lastUser);
	}
	
	// Option Displays for Main Page
	this.optionDisplay = function(opt)
	{
		if (opt == 1)
		{
			document.getElementById('option_select').style.display = "none";
			
			var container = document.getElementById('option_dialog');
			
			if (container.hasChildNodes())
			{
				while(container.childNodes.length >= 1)
					container.removeChild(container.firstChild);
			}
			
			for (var i=0; i<this.fieldList.length; ++i)
			{
				var displayed = false;
				if (this.fieldList[i][1]==1)
					displayed=true;
				var a = "";
				if (displayed)
					a += "<b>";
				a += "<a href=\"#\" onclick=\"DESK.setFieldDisplay("+i+",";
				if (displayed)
					a+="0";
				else
					a+="1";
				a+="); DESK.optionDisplay(1);\">";
				//container.innerHTML += a;
				a += this.fieldList[i][0];
				a += "</a>";
				if (displayed)
					a += "</b>";
				container.innerHTML += a;
				container.innerHTML += "<br />";
			}
				
				
			container.innerHTML += "<br /><a href=\"#\" onclick=\"DESK.optionDisplay(0); DESK.mainPaneDisplay(DESK.lastListXML);\">Close and Apply</a>";
			
			container.style.display = "block";
		}
		else
		{
			document.getElementById('option_dialog').style.display = "none";
			document.getElementById('option_select').style.display = "block";
		}
	}
		
	// Set a fieldDisplay property
	this.setFieldDisplay = function(index, setting)
	{
		this.fieldList[index][1]=setting;
	}
	
	// Display a Request
	this.displayRequest = function(id)
	{
		var url = "request.php?id="+id+"&sid="+DESK.sid;
		DESK.openWindow("FreeDESK Request", url);
	}
	
	// Open a Window
	this.openWindow = function(windowname, url, xsize, ysize, resizeable)
	{
		//alert(url);
		if (xsize == undefined)
			var xsize = 700;
		if (ysize == undefined)
			var ysize = 500;
			
		if (resizeable == undefined)
			var resizeable = 1;
		else if(resizeable)
			resizeable=1;
		else if(!resizeable)
			resizable=0;
		
		var windowopts = "location=0,status=0,scrollbars=1,toolbar=0,width="+xsize+",height="+ysize+",resizeable="+resizeable;
		
		window.open(url, '', windowopts);
	}
	
	// Perform an entity search
	this.entitySearch = function(entity, callback, fields)
	{
		var url = "entity.php?mode=search&entity="+entity;
		
		if (callback != undefined)
			url += "&callback="+callback;
		if (fields != undefined)
		{
			for (var i=0; i<fields.length; ++i)
			{
				url += "&" + fields[i][0] + "=" + fields[i][1]; // escape?
			}
		}
		url += "&sid=" + this.sid;
		
		this.openWindow("Search "+entity, url);
	}
	
	// Open Edit Entity
	this.editEntity = function(entity, keyfield, keyfieldValue)
	{
		var url = "entity.php?mode=edit&entity="+entity+"&keyfield="+keyfield+"&value="+keyfieldValue;
		url += "&sid=" + this.sid;
	
		this.openWindow("Edit "+entity, url);
	}
	
	// Perform an entity creation
	this.entityCreate = function(entity, callback)
	{
		var url = "entity.php?mode=create&entity="+entity;
		
		if (callback != undefined)
			url += "&callback="+callback;
			
		url += "&sid=" + this.sid;
		
		this.openWindow("Create "+entity, url);
	}
	
	// Convert form to query string
	this.formToQuery = function(formid)
	{
		var data = "";
		
		function add(name, value)
		{
			if (value == undefined)
				var value = "";
			
			data += (data.length > 0 ? "&" : ""); // add & if required
		
			data += escape(name).replace(/\+/g, "%2B") + "=";
		
			data += escape(value).replace(/\+/g, "%2B");
		}
		
		var form = document.forms[formid];
		if (!form)
			return "";
		var elements = form.elements;
		
		for (var i=0; i<elements.length; ++i)
		{
			var element = elements[i];
			var type = element.type.toLowerCase();
			var name = element.name;
			
			if (name)
			{
				if (	type == "text" || type == "password" ||
						type == "button" || type == "reset" ||
						type == "file" || type == "submit" ||
						type == "image" || type == "hidden"	|| 
						type == "textarea" )
					add(name, element.value);
					
				else if ( type == "checkbox" && element.checked )
					add(name, element.value ? element.value : "On");
				
				else if ( type == "radio" && element.checked)
					add(name, element.value);
				
				else if ( type.indexOf("select") != -1 )
				{
					for (var x=0; x<element.options.length; ++x)
					{
						var opt = element.options[x];
						if (opt.selected)
							add(name, opt.value ? opt.value : opt.text);
					}
				}
			}
		}
		
		return data;
	}
	
	// API Form Action e.g. save entity
	this.formAPI = function(formid, closeOnComplete, reloadOnComplete, callbackOnComplete)
	{
		if (closeOnComplete == undefined)
			var closeOnComplete = false;
		if (reloadOnComplete == undefined)
			var reloadOnComplete = false;
		if (callbackOnComplete == undefined)
			var callbackOnComplete = false;
		
		
		var q = DESK.formToQuery(formid);
		
		q += "&sid=" + DESK.sid;
		
		var sr = new ServerRequest();
		sr.url = "api.php";
		sr.callback = DESK.formAPIcallback;
		sr.additional = new Array();
		sr.additional[0] = closeOnComplete;
		sr.additional[1] = reloadOnComplete;
		sr.additional[2] = callbackOnComplete;
		sr.Post(q);
	}
	
	this.formAPIcallback = function(xml, additional)
	{
		if (DESK.isError(xml))
		{
			Alerts.add(DESK.getError(xml), 2, 10);
		}
		else
		{
			// We got this far - no DESK error or XML error so we can say success!
			Alerts.add("Operation Successful", 0);
			
			if (additional[2]) // do the javascript first if there is any
			{	
				additional[2](xml);
			}
			
			if (additional[0])
				window.close();
			else if (additional[1])
				window.location.reload();
		}
	}
	
	// Switch a pane
	this.paneSwitch = function(pid, oid)
	{
		var pane = document.getElementById("pane_"+pid);
		var header = document.getElementById("pane_"+pid+"_header");
		
		var child = header.firstChild;
		
		var spans = header.getElementsByTagName("SPAN");
		
		for (var i=0; i<spans.length; ++i)
		{
			var arr = spans[i].id.split("_");
			var opt = arr[arr.length-1];
			
			var contentid = "pane_"+pid+"_"+opt+"_content";
			
			if (oid == opt)
				spans[i].className = "pane_option_selected";
			else
				spans[i].className = "pane_option";
			// Always hide the divs to avoid duplicate display
			document.getElementById(contentid).className = "pane_content_hidden";
		}
		
		var contentid = "pane_"+pid+"_"+oid+"_content";
		document.getElementById(contentid).className = "pane_content";
		
	}
	
	// Open new create request window
	this.createRequest = function(reqclass)
	{
		if (reqclass == undefined)
			var reqclass = "";
		
		var url = "request.php?";
		if (reqclass != "")
			url += "class="+reqclass+"&";
		url += "sid="+DESK.sid;
		
		DESK.openWindow("FreeDESK Request", url);
	}
	
	// Debug data output
	this.debugData = function(container, session)
	{
		var out = "<b>Client-Side JavaScript Debug</b><br /><br />";
		if (session == DESK.sid)
			out += "Session IDs match server and client side<br /><br />";
		else
			out += "Session ID mis-match between client and server<br /><br />";
			
		document.getElementById(container).innerHTML = out;
	}
	
	// Relogin (use current SID and re-post login form)
	this.relogin = function()
	{
		document.forms['login_sid_form'].elements['sid'].value = DESK.sid;
		document.forms['login_sid_form'].submit();
	}
	
	// Start Auto-refreshing
	this.startRefresh = function(interval)
	{
		if (interval == undefined)
			var interval = 30;
		interval = interval * 1000;
		
		DESK.refreshEvent = setInterval(
			function(){ DESK.mainPaneRefresh(); },
			interval );
	}
	
	// Stop Auto-refreshing
	this.stopRefresh = function()
	{
		clearInterval(DESK.refreshEvent);
	}
	
	this.toSeconds = function(hours, minutes, seconds)
	{
		var totalSeconds = (hours * 60 * 60);
		totalSeconds += (minutes * 60);
		totalSeconds += (seconds);
		return totalSeconds;
	}
	
	this.toHMS = function(totalSeconds)
	{
		var hours = 0;
		var minutes = 0;
		var seconds = 0;
		
		if (totalSeconds >= 60*60)
		{
			hours = (totalSeconds/(60*60));
			totalSeconds -= (hours*60*60);
		}
		
		if (totalSeconds >= 60)
		{
			minutes = (totalSeconds/60);
			totalSeconds -= (minutes*60);
		}
		
		seconds = totalSeconds;
		
		var out = new Array();
		
		out[0] = hours;
		out[1] = minutes;
		out[2] = seconds;
		
		return out;
	}
		
	
	// Convert Form H:M:S fields to seconds field
	this.formToSeconds = function(formid, hField, mField, sField, secondsField)
	{
		var hours = (document.forms[formid][hField].value == "") ? 0 : parseInt(document.forms[formid][hField].value);
		var minutes = (document.forms[formid][mField].value == "") ? 0 : parseInt(document.forms[formid][mField].value);
		var seconds = (document.forms[formid][sField].value == "") ? 0 : parseInt(document.forms[formid][sField].value);
		document.forms[formid][secondsField].value = this.toSeconds(hours, minutes, seconds);
	}
	
	// Convert Form Seconds to H:M:S
	this.formToHMS = function(formid, hField, mField, sField, secondsField)
	{
		var hms = this.toHMS(parseInt(document.forms[formid][secondsField].value));
		document.forms[formid][hField].value = hms[0];
		document.forms[formid][mField].value = hms[1];
		document.forms[formid][sField].value = hms[2];
	}
	
	// Go to the mobile interface
	this.goMobile = function()
	{
		window.location.href = "mobile/?sid="+DESK.sid;
	}
}

var DESK = new FreeDESK();

