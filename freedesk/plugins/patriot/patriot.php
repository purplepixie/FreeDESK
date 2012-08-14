<?php 
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

class patriot extends FreeDESK_PIM
{

	function Start()
	{
		$this->DESK->PluginManager->RegisterPIMPage("simplereporting", $this->ID);
		$this->DESK->PluginManager->Register(new Plugin(
			"Patriot Plugin", "0.01", "Visual" ));
		$csspath = $this->webpath . "patriot.css";
		$this->DESK->PluginManager->RegisterCSS($csspath);
		$jspath = $this->webpath . "patriot.js";
		$this->DESK->PluginManager->RegisterScript($jspath);
	}
	
	function BuildMenu()
	{	
		$pmenu = new MenuItem();
		$pmenu->tag = "patriot";
		$pmenu->display = "Patriot";
		$this->DESK->ContextManager->AddMenuItem("patriot", $pmenu);
		
		$col = new MenuItem();
		$col->tag="red";
		$col->display="Red";
		$col->onclick="Patriot.Set(1);";
		$this->DESK->ContextManager->AddMenuItem("patriot",$col);
		
		$col = new MenuItem();
		$col->tag="white";
		$col->display="White";
		$col->onclick="Patriot.Set(2);";
		$this->DESK->ContextManager->AddMenuItem("patriot",$col);
		
		$col = new MenuItem();
		$col->tag="blue";
		$col->display="Blue";
		$col->onclick="Patriot.Set(3);";
		$this->DESK->ContextManager->AddMenuItem("patriot",$col);
		
		$col = new MenuItem();
		$col->tag="autostart";
		$col->display="Auto Start";
		$col->onclick="Patriot.Start();";
		$this->DESK->ContextManager->AddMenuItem("patriot",$col);
		
		$col = new MenuItem();
		$col->tag="autostop";
		$col->display="Auto Stop";
		$col->onclick="Patriot.Stop();";
		$this->DESK->ContextManager->AddMenuItem("patriot",$col);
		
		$col = new MenuItem();
		$col->tag="reset";
		$col->display="Reset";
		$col->onclick="Patriot.Set(0);";
		$this->DESK->ContextManager->AddMenuItem("patriot",$col);
	

	}
	
	
	

}
?>
