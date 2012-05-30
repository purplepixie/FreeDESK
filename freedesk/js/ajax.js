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

// This is the main AJAX functionality for server requests in FreeDESK
// client-side

function ServerRequest()
{
	this.callback = ""; // callback function
	this.url = ""; // URL
	this.additional = array(); // Additional data
	this.xmlhttp = false; // XML HTTP Request Object
	
	this.makeXmlHttp = function()
	{
		if (window.XMLHttpRequest) // all good browsers
		{
			this.xmlhttp = new XMLHttpRequest;
			if (this.xmlhttp.overrideMimeType)
				this.xmlhttp.overrideMimeType("text/xml");
		}
		else if (window.ActiveXObject) // Satan's Browser
		{
			try
			{
				this.xmlhttp = new ActiveXObject("Msxml2.HTTP"); // Newer
			}
			catch(e)
			{
				try
				{
					this.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch(ex)
				{
					// No Microsoft WHAT HAVE YOU DONE??!?
				}
			}
		}
		
		if (!this.xmlhttp)
			alert("Error: Cannot Create XMLHTTP Object");
	}
	
	this.Get = function()
	{
		if (!this.xmlhttp)
			this.makeXmlHttp();
		this.xmlhttp.open('GET', this.url, true);
		this.xmlhttp.ajax = this;
		this.xmlhttp.onreadystatechange = function()
		{
			if (this.readyState == 4)
			{
				if (this.status == 200)
				{
					if (this.responseXML)
					{
						this.ajax.callback( this.responseXML, this.ajax.additional );
					}
					else
					{
						alert("AJAX XML Error: Invalid or Null\nBody:\n"+this.responseText);
					}
				}
				else
				{
					alert("AJAX Server Code: "+this.status+"\nURL: "+this.ajax.url);
				}
			}
		}
		this.xmlhttp.send();
	}
	

