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
 * FreeDESK entity search client-side code
**/

function FreeDESK_Search()
{
	this.entity = ""; // search entity
	
	this.searchformid = "entitysearch"; // id of search form
	
	this.displayid = "searchresults"; // id of search results container
	
	this.autosid = true; // Automatically add the DESK.sid
	
	this.autoentity = true; // Automatically add the entity
	
	this.autoreset = true; // Reset on every search call
	
	this.automode = true; // automatically set entity_search mode
	
	this.callback = null; // parent form callback function
	
	this.callbackOnSingle = false; // call callback if only one result
	
	this.data = "";
	
	// Add an item to the data string
	this.add = function(name, value)
	{
		if (value == undefined)
			var value = "";
			
		this.data += (this.data.length > 0 ? "&" : ""); // add & if required
		
		this.data += escape(name).replace(/\+/g, "%2B") + "=";
		
		this.data += escape(value).replace(/\+/g, "%2B");
	}
	
	// Add a string to the data string (pre-escaped), optional flag add & if required (default true)
	this.addString = function(str, autoAmp)
	{
		if ( autoAmp == undefined || autoAmp )
			this.data += (this.data.length > 0 ? "&" : "");
		
		this.data += str;
	}
	
	// Reset
	this.reset = function()
	{
		this.data = "";
	}

	
	// Load a Request List to the Main Pane
	this.search = function(start, results)
	{
		if (this.autoreset)
			this.reset();
			
		if(this.autoentity)
			this.add("entity", this.entity);
		
		if (this.automode)
			this.add("mode", "entity_search");
	
		if (start == undefined)
			start = 0;
		if (results == undefined)
			results = 30;
		var sr = new ServerRequest();
		
		// Load the data from the form
		this.addString(DESK.formToQuery(this.searchformid));
		
		this.add("start", start);
		this.add("limit", results);
		
		if (this.autosid)
			this.add("sid", DESK.sid);
		
		
		sr.callback = DESKSearch.searchResults;
		sr.url = "api.php";
		
		sr.Post(this.data);
	}
	
	// Display a request list in the main pane
	this.searchResults = function(xml)
	{
		if (DESK.isError(xml))
		{
			alert(DESK.getError(xml));
			return ;
		}
		var table = document.createElement("table"); // table for results
		table.border=0;
		table.width="100%";
		table.className="searchList";
		
		var container = document.getElementById(DESKSearch.displayid);
		
		if (container.hasChildNodes())
		{
			while(container.childNodes.length >= 1)
				container.removeChild(container.firstChild);
		}
		
		container.appendChild(table);
		
		var meta = xml.getElementsByTagName("meta")[0];
		
		var start = parseInt(meta.getElementsByTagName("start")[0].firstChild.nodeValue);
		var limit = parseInt(meta.getElementsByTagName("limit")[0].firstChild.nodeValue);
		var count = parseInt(meta.getElementsByTagName("count")[0].firstChild.nodeValue);
		var keyfield = (meta.getElementsByTagName("keyfield")[0].textContent==undefined) ?
			meta.getElementsByTagName("keyfield")[0].firstChild.nodeValue : meta.getElementsByTagName("keyfield")[0].textContent ;
		
		var display = "Displaying results "+(start+1)+" to "+(start+limit)+" of "+count;
		var dispdivtop = document.createElement("div");
		var dispdivbot = document.createElement("div");
		dispdivtop.innerHTML = display;
		dispdivbot.innerHTML = display;
		container.appendChild(dispdivtop);
		
		container.appendChild(table);
		
		var entities = xml.getElementsByTagName("entity");
		
		var fieldcount = 0;
		var rowcount = 0;
		var callbacknow = false;
		
		if (DESKSearch.callback != null && entities.length==1)
			callbacknow=true;
		
		for (var i=0; i<entities.length; ++i)
		{
			var keyfieldval = "";
			var entity = entities[i];
			var row = table.insertRow(table.getElementsByTagName("tr").length);
			row.className="row"+rowcount;
			if ( ++rowcount > 1)
				rowcount = 0;
			var fields = entity.getElementsByTagName("field");
			
			fieldcount=0;
			
			for (var z=0; z<fields.length; ++z)
			{
				var id = fields[z].attributes.getNamedItem("id").value;
				var cell = row.insertCell(z);
				var data = (fields[z].textContent == undefined) ? fields[z].firstChild.nodeValue : fields[z].textContent;
				if (id == keyfield)
				{
					keyfieldval = data;
					if (DESKSearch.callback != null)
					{
						data = "<a href=\"#\" onclick=\"DESKSearch.callback('"+keyfieldval+"');\">"+keyfieldval+"</a>";
						if (callbacknow)
						{
							DESKSearch.callback(keyfieldval);
							window.close();
						}
					}
				}
				cell.innerHTML = data;
				++fieldcount;
			}
			
			var edit = "<a href=\"#\" onclick=\"DESK.editEntity('"+DESKSearch.entity+"','"+keyfield+"','"+keyfieldval+"');\">Edit</a>";
			var cell = row.insertCell(-1);
			cell.innerHTML = edit;
		}
		
		var prevCell = "&nbsp;";
		
		if (start>0)
		{
			prevCell="<a href=\"#\" onclick=\"DESKSearch.search("+(start-limit)+","+limit+");\">&lt;&lt; Previous</a>";
		}
		
		var nextCell = "&nbsp;";
		
		if ( (start+limit) < count )
		{
			nextCell="<a href=\"#\" onclick=\"DESKSearch.search("+(start+limit)+","+limit+");\">&gt;&gt; Next</a>";
		}
		
		var spanWidth = fieldcount-1; // allowing for the edit
		
		var row = table.insertRow(0);
		var rowb = table.insertRow(-1);
		var cell = row.insertCell(0);
		var cellb = rowb.insertCell(0);
		cell.innerHTML = prevCell;
		cellb.innerHTML = prevCell;
		
		for (var i=0; i<spanWidth; ++i)
		{
			cell = row.insertCell(i+1);
			cellb = rowb.insertCell(i+1);
			cell.innerHTML = "&nbsp;";
			cellb.innerHTML = "&nbsp;";
		}
		cell=row.insertCell(spanWidth+1);
		cellb=rowb.insertCell(spanWidth+1);
		
		cell.innerHTML = nextCell;
		cellb.innerHTML = nextCell;
		
		container.appendChild(dispdivbot);
		
	}
	
}


var DESKSearch = new FreeDESK_Search();

