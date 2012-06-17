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
		var form = document.forms[this.searchformid];
		if (!form)
			return ;
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
					this.add(name, element.value);
					
				else if ( type == "checkbox" && element.checked )
					this.add(name, element.value ? element.value : "On");
				
				else if ( type == "radio" && element.checked)
					this.add(name, element.value);
				
				else if ( type.indexOf("select") != -1 )
				{
					for (var x=0; x<element.options.length; ++x)
					{
						var opt = element.options[x];
						if (opt.selected)
							this.add(name, opt.value ? opt.value : opt.text);
					}
				}
			}
		}
		
		if (this.autosid)
			this.add("sid", DESK.sid);
		
		sr.callback = DESKSearch.searchResults;
		sr.url = "api.php";
		//alert(this.data);
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
		
		var rowcount = 0;
		for (var i=0; i<entities.length; ++i)
		{
			var keyfieldval = "";
			var entity = entities[i];
			var row = table.insertRow(table.getElementsByTagName("tr").length);
			row.className="row"+rowcount;
			if ( ++rowcount > 1)
				rowcount = 0;
			var fields = entity.getElementsByTagName("field");
			
			for (var z=0; z<fields.length; ++z)
			{
				var id = fields[z].attributes.getNamedItem("id").value;
				var cell = row.insertCell(z);
				var data = (fields[z].textContent == undefined) ? fields[z].firstChild.nodeValue : fields[z].textContent;
				if (id == keyfield)
					keyfieldval = data;
				cell.innerHTML = data;
			}
			
			var edit = "<a href=\"#\" onclick=\"DESK.editEntity('"+DESKSearch.entity+"','"+keyfield+"','"+keyfieldval+"');\">Edit</a>";
			var cell = row.insertCell(-1);
			cell.innerHTML = edit;
		}
		
		container.appendChild(dispdivbot);
		
	}
	
}


var DESKSearch = new FreeDESK_Search();

