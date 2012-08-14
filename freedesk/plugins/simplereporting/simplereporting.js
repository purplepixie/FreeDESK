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

function SimpleReporting()
{
	this.runReport = function(formid)
	{
		if (formid == undefined)
			var formid = "simplereport";
		
		var sYear = document.forms[formid]["sYear"].value;
		var sMonth = document.forms[formid]["sMonth"].value;
		var sDay = document.forms[formid]["sDay"].value;
		
		var fYear = document.forms[formid]["fYear"].value;
		var fMonth = document.forms[formid]["fMonth"].value;
		var fDay = document.forms[formid]["fDay"].value;
		
		var opts = "sYear="+sYear+"&sMonth="+sMonth+"&sDay="+sDay;
		opts += "&fYear="+fYear+"&fMonth="+fMonth+"&fDay="+fDay;
		
		opts += "&runreport=1";
		
		DESK.loadSubpage("simplereporting",opts);
	}
}

var simpleReporting = new SimpleReporting();
