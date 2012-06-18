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
 * The alert pane manager for FreeDESK
**/
function FreeDESK_AlertPane()
{
	this.container = document.getElementById('alert_pane');
	
	this.random = function()
	{
		var len=32;
		var chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		var randomstring = "";
		for (var i=0; i<len; ++i)
		{
			var randchar = Math.floor(Math.random() * chars.length);
			randomstring += chars.substring(randchar, randchar+1);
		}
		return randomstring;
	}
	
	// Add an alert
	// text: text to appear
	// level: 0 green, 1 (default) yellow, 2 red
	// autoRemove: seconds before autoRemove (0 = keep forever), default 5s
	this.add = function(text, level, autoRemove)
	{
		if (level == undefined)
			var level = 1;
		if (autoRemove == undefined)
			var autoRemove = 5;
			
		if (this.container == null) // not initialised at startup
			this.container = document.getElementById('alert_pane');
		var div = document.createElement('div');
		var id = "alert_"+this.random();
		div.setAttribute('id', id);
		div.className = "alert_"+level;
		
		var content = document.createElement('span');
		content.innerHTML = text;
		
		var close = document.createElement('span');
		close.className = "alertclose";
		close.innerHTML = "<a href=\"#\" onclick=\"Alerts.close('"+id+"');\">X</a>";
		
		div.appendChild(content);
		div.appendChild(close);
		
		this.container.appendChild(div);
		
		if (autoRemove > 0)
		{
			setTimeout(function(){Alerts.close(id); }, autoRemove*1000);
		}
	}
	
	this.close = function(id)
	{
		var child = document.getElementById(id);
		if (child != undefined)
			this.container.removeChild(child);
	}
	
	this.clear = function()
	{
		if (this.container.hasChildNodes())
		{
			while(this.container.childNodes.length >= 1)
				this.container.removeChild(this.container.firstChild);
		}
	}
	
	this.randomTest = function()
	{
		var lvl = Math.floor(Math.random() * 3);
		var txt = "Hello "+this.random()+ " "+lvl;
		this.add(txt, lvl);
	}
}

Alerts = new FreeDESK_AlertPane();

