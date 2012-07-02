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

class test extends FreeDESK_PIM
{

	function Start()
	{
		// Let's include some JavaScript - first the name of our script
		$jspath = $this->webpath . "test.js";
		// Then register it for inclusion
		$this->DESK->PluginManager->RegisterScript($jspath);
		
		// We also want to register our page
		$this->DESK->PluginManager->RegisterPIMPage("testpage", $this->ID);
		
		// And our API call
		$this->DESK->PluginManager->RegisterPIMAPI("my_test_api_call", $this->ID);
	}
	
	function BuildMenu()
	{	
		// Now adding a menu option - first to an existing menu, the 'home' menu
		// Create the actual menu item
		$testmenu = new MenuItem();
		$testmenu->tag = "test";
		$testmenu->display = "Test Item";
		$testmenu->onclick = "PIMTest.say('hello world');";
		// And register it
		$this->DESK->ContextManager->AddMenuItem("home", $testmenu);
		
		// And now for a totally new menu displayed...
		// The main menu item here
		$menu = new MenuItem();
		$menu->tag="testmenu";
		$menu->display="Test Menu";
		// no on-click events for this top-level one (though we could if we want)
		
		// And create the submenu items for it
		$hello = new MenuItem();
		$hello->tag="hello";
		$hello->display="Say Hello";
		$hello->onclick="PIMTest.say('Hello!');";
		// Add to our new menu
		$menu->submenu[] = $hello;
		
		// Now we could do the same (use $menu->submenu[]) again but rather we'll register it...
		$this->DESK->ContextManager->AddMenuItem("testmenu", $menu);
		
		// And then add another menuitem directly to it like we did for the home menu
		$goodbye = new MenuItem();
		$goodbye->tag="goodbye";
		$goodbye->display="Say Goodbye";
		$goodbye->onclick="PIMTest.say('Goodbye!');";
		// Register...
		$this->DESK->ContextManager->AddMenuItem("testmenu",$goodbye);
		// because we used a pre-existing tag "testmenu" it will be appended
		
		// And now let's put a link in to our page called testpage
		$page = new MenuItem();
		$page->tag="testpage";
		$page->display="Test PIM Page";
		$page->onclick="DESK.loadSubpage('testpage');";
		$this->DESK->ContextManager->AddMenuItem("testmenu",$page);
		
	}
	
	function Page($page)
	{
		if ($page == "testpage")
		{
			echo "<h3>This is our page created by the test PIM</h3>\n";
			
			echo "<a href=\"#\" onclick=\"PIMTest.say('hello');\">Say Hello</a><br /><br />\n";
			
			// Let's show an API example
			echo "<form id=\"my_test_api_form\" onsubmit=\"return false;\">\n";
			echo "<input type=\"hidden\" name=\"mode\" value=\"my_test_api_call\" />\n";
			$onclick="DESK.formAPI('my_test_api_form');";
			echo "<input type=\"submit\" value=\"Make a call to the API with my_test_api_call\" onclick=\"".$onclick."\" />\n";
			echo "</form><br /><br />\n";
		}
	}
	
	function API($mode)
	{
		if ($mode == "my_test_api_call")
		{
			// Do nothing just return XML for a successful operation!
			$xml = new xmlCreate();
			$xml->charElement("operation","1");
			echo $xml->getXML(true);
			exit();
		}
	}

}
?>
