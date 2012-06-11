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

class DESKTest
{
	var $type = "";
	var $id = 0;
	var $passed = false;
	var $warning = false;
	var $text = "";
	var $desc = "";
	
	function GetID()
	{
		return $this->type.".".$this->id;
	}
	
	function DESKTest($type="", $id=0, $desc="")
	{
		$this->type = $type;
		$ths->id = $id;
		$this->desc = $desc;
	}
}

class DESKTester
{
	var $tests = array();
	var $testcount = 0;
	var $passed = 0;
	var $warning = 0;
	var $failed = 0;
	var $warnlist = array();
	var $faillist = array();
	
	var $DESK = null;
	
	function Add($test)
	{
		$this->tests[$test->GetID()]=$test;
		++$this->testcount;
		if ($test->passed)
			++$this->passed;
		else
		{
			++$this->failed;
			$this->faillist[]=$test->GetID();
		}
		if ($test->warning)
		{
			++$this->warning;
			$this->warnlist[]=$test->GetID();
		}
	}
	
	function Summary()
	{
		if ($this->warning > 0)
		{
			echo "WARNINGS:\n";
			foreach($this->warnlist as $warn)
			{
				echo $warn." : ".$this->tests[$warn]->desc." : ".$this->tests[$warn]->text."\n";
			}
			echo "\n";
		}
		
		if ($this->failed > 0)
		{
			echo "FAILURES:\n";
			foreach($this->faillist as $fail)
			{
				echo $fail." : ".$this->tests[$fail]->desc." : ".$this->tests[$fail]->text."\n";
			}
			echo "\n";
		}

		echo "FreeDESK Unit Test Summary\n";
		echo "Tested : ".$this->testcount."\n";
		echo "Passed : ".$this->passed."\n";
		echo "Warning: ".$this->warning."\n";
		echo "Failed : ".$this->failed."\n";
		echo "\n";
	}
}

$desktest = new DESKTester();

require("unittest/core.php");
require("unittest/context.php");
require("unittest/request.php");	

//print_r($desktest->DESK->PluginManager->GetAll());
		
$desktest->Summary();
		
?>
