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
 * FreeDESK client-side request code
**/

function FreeDESK_Request()
{
	var customerIsSelected = false;

	this.searchCustomer = function()
	{
		var url = "entity.php?mode=search&entity=customer&";
		url += DESK.formToQuery('customersearch');
		url += "&searchnow=1&callback=DESKRequest.searchCustomerCallback&onereturn=1";
		url += "&sid=" + DESK.sid;
		
		DESK.openWindow("FreeDESK Customer Search", url);
	}
	
	this.searchCustomerCallback = function(customerid)
	{
		document.getElementById('customer_id').innerHTML = customerid;
		document.getElementById('customer_select').style.display = 'none';
		document.getElementById('customer_details').style.display = 'block';
		DESKRequest.customerSelected(true);
	}
	
	this.searchCustomerAgain = function()
	{
		document.getElementById('customer_details').style.display = 'none';
		document.getElementById('customer_select').style.display = 'block';
		DESKRequest.customerSelected(false);
	}
	
	this.customerSelected = function(sel)
	{
		DESKRequest.customerIsSelected = sel;
	}
	
	this.Create = function(closeOnComplete)
	{
		if (closeOnComplete == undefined)
			var closeOnComplete = false;
		
		
		if (!this.customerIsSelected)
		{
			alert("Must select a customer");
			return;
		}
		
		var detail = DESK.formToQuery("request_create");
		var mode = "request_create";
		var add = "customer="+document.getElementById('customer_id').innerHTML;
		
		var data = "mode="+mode+"&"+add+"&"+detail+"&sid="+DESK.sid;
		
		var sr = new ServerRequest();
		
		var add = Array();
		
		if (closeOnComplete)
			add[0]=1;
		else
			add[0]=0;
		
		sr.callback = DESKRequest.createCallback;
		sr.url = "api.php";
		sr.additional = add;
		sr.Post(data);
	}
	
	this.createCallback = function(xml, add)
	{
		if (DESK.isError(xml))
		{
			Alerts.add(DESK.getError(xml), 2, 10);
		}
		else
		{
			var req = xml.getElementsByTagName("request")[0];
			var id = (req.textContent == undefined) ? req.firstChild.nodeValue : req.textContent;
			var url = "request.php?id="+id+"&sid="+DESK.sid;
			
			if (document.forms["request_create"]["emailflag"].checked) // email customer
			{
				var update = document.forms["request_create"]["update"].value;
				var emailurl = "email.php?template=open&request="+id+"&update="+encodeURI(update)+"&sid="+DESK.sid;
				DESK.openWindow("Email", emailurl);
			}
			
			if (add[0] == 1)
				window.close();
			else
				window.location.href = url;
		}
	}
	
	this.emailUpdateCheck = function()
	{
		if (document.forms["request_update"]["emailflag"].checked) // email customer
		{
			var req = currentRequestID;
			var update = document.forms["request_update"]["update"].value;
			var template = "update";
			if (document.forms["request_update"]["status"].value == "0")
				template = "close";
				
			var emailurl = "email.php?template="+template+"&request="+req+"&update="+encodeURI(update)+"&sid="+DESK.sid;
			DESK.openWindow("Email", emailurl);
		}
	}
	
}

var DESKRequest = new FreeDESK_Request();

