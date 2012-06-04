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

	this.login_action = function(responseXML)
	{
		//alert(responseXML);
		var txt = document.createTextNode(responseXML);
		document.getElementById("login_content").appendChild(txt);
	}

	this.login_click=function()
	{
		var req = new ServerRequest();
		req.url = "api.php?mode=login&type=user&username="
			+document.getElementById("login_username").value
			+"&password="
			+document.getElementById("login_password").value;
		req.callback = login_action;
		req.Get();
	}


}

var DESK = new FreeDESK();

