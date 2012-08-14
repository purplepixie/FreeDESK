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

/**
 * Error Codes
**/
class ErrorCode
{
	const FailedLogin = 101;
	const SessionExpired = 102;
	
	const UnknownMode = 201;
	
	const EntityError = 300;
	
	const Forbidden = 403;
	const ResourceNotFound = 404;
	
	const UnknownRequest = 604;
	
	const OperationFailed = 700;
}

/**
 * FreeDESK Error
**/
class FreeDESK_Error
{
	/**
	 * Code
	**/
	var $Code = 0;
	/**
	 * Text
	**/
	var $Text = "";
	/**
	 * Constructor
	 * @param int $code Error Code (ErrorCode::)
	 * @param string $text Text (optional, default "")
	**/
	function FreeDESK_Error($code, $text="")
	{
		$this->Code=$code;
		$this->Text=$text;
	}
	/**
	 * Get Error as XML
	 * @param bool $header Put on XML header (optional, default false)
	 * @return string XML data
	**/
	function XML($header=false)
	{
		$xml = new xmlCreate();
		$data=array("code"=>$this->Code);
		$xml->startElement("error",$data);
		$xml->charElement("code",$this->Code);
		$xml->charElement("text",$this->Text,0,false,true);
		$xml->endElement("error");
		return $xml->getXML($header);
	}
}
?>
