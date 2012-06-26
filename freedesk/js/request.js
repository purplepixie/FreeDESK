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
	
}

var DESKRequest = new FreeDESK_Request();

