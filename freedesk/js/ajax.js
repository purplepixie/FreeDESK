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
	this.additional = new Array(); // Additional data
	this.xmlhttp = false; // XML HTTP Request Object
	this.xmlrequest = true; // Is it an XML Request?
	this.async = true; // Is it asynchronous (not yet implemented)
	this.randomise = true; // Set a random string to query to avoid caching
	
	this.makeXmlHttp = function()
	{
		if (window.XMLHttpRequest) // all good browsers and indeed IE nowadays
		{
			this.xmlhttp = new XMLHttpRequest;
			if (this.xmlrequest && this.xmlhttp.overrideMimeType)
				this.xmlhttp.overrideMimeType("text/xml");
		}
		else if (window.ActiveXObject) // Satan's Browser (old IE or Exploiter 7+ with XMLHttp Disabled)
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
		else
		{
			// Dear lord are we running in lynx ??!?
		}
		
		if (!this.xmlhttp)
			alert("Error: Cannot Create XMLHTTP Object");
	}
	
	this.randomString = function(len)
	{
		if (len == undefined)
			len = 32;
		var chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		var randomstring = "";
		for (var i=0; i<len; ++i)
		{
			var randchar = Math.floor(Math.random() * chars.length);
			randomstring += chars.substring(randchar, randchar+1);
		}
		return randomstring;
	}
	
	this.Get = function()
	{
		if (!this.xmlhttp)
			this.makeXmlHttp();
			
		var myurl = this.url;
		if (this.randomise)
		{
			if (myurl.indexOf("?") == -1)
				myurl += "?nocache="+this.randomString();
			else
				myurl += "&nocache="+this.randomString();
		}
		
		this.xmlhttp.open('GET', myurl, true);
		this.xmlhttp.ajax = this;
		if (this.xmlrequest)
		{
			this.xmlhttp.onreadystatechange = function()
			{
				if (this.readyState == 4)
				{
					if (this.status == 200)
					{
						if (this.responseXML)
						{
							if (DESK.isError(this.responseXML))
							{
								if (DESK.getErrorCode(this.responseXML)==102) // expired session
								{
									DESK.show_login(DESK.getError(this.responseXML));
									return;
								}
							} // Hand errors other than 102 through to the callback routine to handle
							this.ajax.callback(this.responseXML, this.ajax.additional );
						}
						else
						{
							alert("AJAX XML Error: Invalid or Null\nBody:\n"+this.responseXML);
						}
					}
					else
					{
						alert("AJAX Server Code: "+this.status+"\nURL: "+this.ajax.url);
					}
				}
			}
		}
		else
		{
			// HTML Request
			this.xmlhttp.onreadystatechange = function()
			{
				if (this.readyState == 4)
				{
					if (this.status == 200)
					{
						if (this.responseText)
						{
							this.ajax.callback(this.responseText, this.ajax.additional);
						}
						else
						{
							alert("AJAX Text Error: Invalid or Null Body\nBody:\n"+this.responseText);
						}
					}
					else
					{
						alert("AJAX Server Code: "+this.status+"\nURL: "+this.ajax.url);
					}
				}
			}
		}
		this.xmlhttp.send();
	}
	
	
	
	this.Post = function(postdata)
	{
		if (!this.xmlhttp)
			this.makeXmlHttp();
		
		this.xmlhttp.open('POST', this.url, true);
		this.xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		this.xmlhttp.ajax = this;
		if (this.xmlrequest)
		{
			this.xmlhttp.onreadystatechange = function()
			{
				if (this.readyState == 4)
				{
					if (this.status == 200)
					{
						if (this.responseXML)
						{
							if (DESK.isError(this.responseXML))
							{
								if (DESK.getErrorCode(this.responseXML)==102) // expired session
								{
									DESK.show_login(DESK.getError(this.responseXML));
									return;
								}
							} // Hand errors other than 102 through to the callback routine to handle
							this.ajax.callback(this.responseXML, this.ajax.additional );
						}
						else
						{
							alert("AJAX XML Error: Invalid or Null\nBody:\n"+this.responseXML);
						}
					}
					else
					{
						alert("AJAX Server Code: "+this.status+"\nURL: "+this.ajax.url);
					}
				}
			}
		}
		else
		{
			// HTML Request
			this.xmlhttp.onreadystatechange = function()
			{
				if (this.readyState == 4)
				{
					if (this.status == 200)
					{
						if (this.responseText)
						{
							this.ajax.callback(this.responseText, this.ajax.additional);
						}
						else
						{
							alert("AJAX Text Error: Invalid or Null Body\nBody:\n"+this.responseText);
						}
					}
					else
					{
						alert("AJAX Server Code: "+this.status+"\nURL: "+this.ajax.url);
					}
				}
			}
		}
		this.xmlhttp.send(postdata);
	}
	
}

