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
	// List of display fields for request list
	this.fieldList = new Array();

	// XML of last request list fetched (for redisplay)
	this.lastListXML = null;

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
		//alert(teamid+" "+username);
		var sr = new ServerRequest();
		sr.url = "api.php?mode=requests_assigned&teamid="+teamid+"&username="+username+"&sid="+this.sid;
		sr.callback = DESK.mainPaneDisplay;
		sr.Get();
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
				cell.innerHTML = DESK.fieldList[i][0];
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
		alert(id);
	}
	
	// Open a Window
	this.openWindow = function(windowname, url, xsize, ysize, resizeable)
	{
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
		
		window.open(url, windowname, windowopts);
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
						type == "image" || type == "hidden"	)
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
	this.formAPI = function(formid, closeOnComplete)
	{
		if (closeOnComplete == undefined)
			var closeOnComplete = false;
		
		var q = DESK.formToQuery(formid);
		
		q += "&sid=" + DESK.sid;
		
		var sr = new ServerRequest();
		sr.url = "api.php";
		sr.callback = DESK.formAPIcallback;
		sr.additional[0] = closeOnComplete;
		sr.Post(q);
	}
	
	this.formAPIcallback = function(xml, additional)
	{
		if (DESK.isError(xml))
		{
			alert(DESK.getError(xml));
		}
		else
		{
			// Message ?
			
			if (additional[0])
				window.close();
		}
	}
		
}

var DESK = new FreeDESK();

