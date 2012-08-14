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

class simplereporting extends FreeDESK_PIM
{

	function Start()
	{
		$this->DESK->PluginManager->RegisterPIMPage("simplereporting", $this->ID);
		$this->DESK->PluginManager->Register(new Plugin(
			"Simple Reporting", "0.02", "Reporting" ));
		$this->DESK->PermissionManager->Register("simple_reporting", false);
		$jspath = $this->webpath . "simplereporting.js";
		$this->DESK->PluginManager->RegisterScript($jspath);
	}
	
	function BuildMenu()
	{	
		if ($this->DESK->ContextManager->Permission("simple_reporting"))
		{
			$currentItems = $this->DESK->ContextManager->MenuItems();
			if (!isset($currentItems['reporting']))
			{
				$repmenu = new MenuItem();
				$repmenu->tag = "reporting";
				$repmenu->display = "Reporting";
				$this->DESK->ContextManager->AddMenuItem("reporting", $repmenu);
			}
		
			$sReport = new MenuItem();
			$sReport->tag="simplereporting";
			$sReport->display="Simple Reporting";
			$sReport->onclick = "DESK.loadSubpage('simplereporting');";
			$this->DESK->ContextManager->AddMenuItem("reporting",$sReport);
		}
	}
	
	function Page($page)
	{
		if ($page == "simplereporting" && $this->DESK->ContextManager->Permission("simple_reporting"))
		{
			echo "<h3>Simple Reporting</h3>\n";
		
			$cYear = date("Y");
			$cMonth = date("m");
			$cDay = date("d");
			
			$sYear = (isset($_REQUEST['sYear'])) ? $_REQUEST['sYear'] : $cYear;
			$sMonth = (isset($_REQUEST['sMonth'])) ? $_REQUEST['sMonth'] : 1;
			$sDay = (isset($_REQUEST['sDay'])) ? $_REQUEST['sDay'] : 1;
			$fYear = (isset($_REQUEST['fYear'])) ? $_REQUEST['fYear'] : ($cYear+1);
			$fMonth = (isset($_REQUEST['fMonth'])) ? $_REQUEST['fMonth'] : 1;
			$fDay = (isset($_REQUEST['fDay'])) ? $_REQUEST['fDay'] : 1;
			
			if ($sMonth < 10)
				$sMonth = "0".$sMonth;
			if ($sDay < 20)
				$sDay = "0".$sDay;
			if ($fMonth < 10)
				$fMonth = "0".$fMonth;
			if ($fDay < 10)
				$fDay = "0".$fDay;
			
			echo "<form id=\"simplereporting\" onsubmit=\"return false;\">\n";
			echo "<table>\n";
			echo "<tr>\n";
			echo "<td>Start</td>\n";
			echo "<td>\n";
			echo "<select name=\"sYear\">";
			for($i=2011; $i<=($cYear+5); ++$i)
			{
				if ($i == $sYear)
					$s=" selected";
				else
					$s="";
				echo "<option value=\"".$i."\"".$s.">".$i."</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";
			
			echo "<td>\n";
			echo "<select name=\"sMonth\">";
			for($i=1; $i<=12; ++$i)
			{
				if ($i == $sMonth)
					$s=" selected";
				else
					$s="";
				echo "<option value=\"".$i."\"".$s.">".$i."</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";
			
			echo "<td>\n";
			echo "<select name=\"sDay\">";
			for($i=1; $i<=31; ++$i)
			{
				if ($i == $sDay)
					$s=" selected";
				else
					$s="";
				echo "<option value=\"".$i."\"".$s.">".$i."</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";
			
			echo "</tr>\n";
			
			
			echo "<tr>\n";
			echo "<td>Finish</td>\n";
			echo "<td>\n";
			echo "<select name=\"fYear\">";
			for($i=2011; $i<=($cYear+5); ++$i)
			{
				if ($i == $fYear)
					$s=" selected";
				else
					$s="";
				echo "<option value=\"".$i."\"".$s.">".$i."</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";
			
			echo "<td>\n";
			echo "<select name=\"fMonth\">";
			for($i=1; $i<=12; ++$i)
			{
				if ($i == $fMonth)
					$s=" selected";
				else
					$s="";
				echo "<option value=\"".$i."\"".$s.">".$i."</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";
			
			echo "<td>\n";
			echo "<select name=\"fDay\">";
			for($i=1; $i<=31; ++$i)
			{
				if ($i == $fDay)
					$s=" selected";
				else
					$s="";
				echo "<option value=\"".$i."\"".$s.">".$i."</option>\n";
			}
			echo "</select>\n";
			echo "</td>\n";
			
			echo "</tr>\n";
			
			echo "<tr>\n";
			echo "<td colspan=\"4\">\n";
			echo "<input type=\"submit\" value=\"Run Report\" onclick=\"simpleReporting.runReport('simplereporting');\" />\n";
			echo "</td></tr>";
			
			echo "</table>\n";
			echo "</form>\n";
			
			if (isset($_REQUEST['runreport']))
			{
				echo "<br /><br />";
				echo "<h3>Report for ".$sYear."-".$sMonth."-".$sDay." to ".$fYear."-".$fMonth."-".$fDay."</h3>\n";
			}
		}
	}
	
	

}
?>
