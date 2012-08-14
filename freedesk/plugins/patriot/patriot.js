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

function patriotPlugin()
{
	this.mode = 0;
	
	this.timer = false;
	
	this.interval = 500;
	
	this.Set = function(newmode)
	{
		//alert(newmode);
		var addon = "";
		if (newmode != 0)
			addon = newmode;
		this.mode = newmode;
		
		document.getElementById('header').className = "header"+addon;
		document.getElementById('footer').className = "footer"+addon;
	}
	
	this.Start = function()
	{
		this.timer = setInterval(function(){ Patriot.Rand(); }, this.interval);
	}
	
	this.Stop = function()
	{
		clearInterval(this.timer);
		this.Set(0);
	}
	
	this.Rand = function()
	{
		this.Set(Math.floor(Math.random()*3)+1);
	}
	
}

var Patriot = new patriotPlugin();

