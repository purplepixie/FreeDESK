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
 * Main xmlCreate class object
*/
class xmlCreate
{

/**
 * Contains XML Output
*/
var $xml = "";

/**
 * Contains open tags
 * Not yet implemented 
*/
var $open_tags = array();

/**
 * Contains Depth of Elements
*/
var $depth = 0;

/** 
 * Control the depth (variance)
 * @param integer $varience What to alter the depth by
*/
function setDepth($varience)
{
	$this->depth += $varience;
}
	
/** 
 * Pad to depth
*/
function pad()
{
	if ($this->depth<1) return "";
	return str_pad("",$this->depth," ");
}

/**
 * Start building an open-ended XML element
 * @param string $element Name of the element
 * @param array $vars Optional array of key values to use (or int 0 to skip)
 * @param boolean $single Optional indicator of single element if true
 * @param boolean $newline Optional insert newline at the end
*/
function startElement($element,$vars=0,$single=false,$newline=true)
{
	$this->xml.=$this->pad();
	$this->xml.="<".$element;
	if ( ($vars!=0) && (is_array($vars)) )
	{
		foreach($vars as $key => $val)
		{
			$this->xml.=" ".$key."=\"".$val."\"";
		}
	}
	if ($single) $this->xml.=" /";
	$this->xml.=">";
	if ($newline) $this->xml.="\n";
	if (!$single) $this->setDepth(1);
}
	
	
/**
 * End an open-ended XML element
 * @param string $element Name of the element
 * @param boolean $pad Optional use padding (default true)
*/
function endElement($element,$pad=true)
{
	$this->setDepth(-1);
	if ($pad) $this->xml.=$this->pad();
	$this->xml.="</".$element.">\n";
}

/**
 * Insert data (textual/char data)
 * @param string $data Actual data to insert
 * @param boolean $cdata Wrap with CDATA (optional, default NO)
 * @param boolean $htmlcode Use HTML special characters (optional, default NO)
 * @param boolean $newline Insert newline after data (optional, default YES)
*/
function charData($data, $cdata=false, $htmlcode=false, $newline=true)
{
	$dataline="";
	if ($cdata) $dataline.="<![CDATA[";
	if ($htmlcode) $data=htmlspecialchars($data);
	$dataline.=$data;
	if ($cdata) $dataline.="]]>";
	if ($newline) $dataline.="\n";
	$this->xml.=$dataline;
}
	
/**
 * Single element wrapper for (@link startElement)
 * @param string $element Element Name
 * @param array $vars Optional variable array
*/
function singleElement($element,$vars=0)
{
	$this->startElement($element,$vars,true);
}
	
/**
 * Single char-element wrapper
 * @param string $element Element Name
 * @param string $data Element Data Content
 * @param array $vars Optional array element variables (int 0 to skip)
 * @param boolean $htmlchars Optional convert data to html special chars (default false)
 * @param boolean $cdata Optional enclose char data in CDATA block (default false)
*/
function charElement($element,$data,$vars=0,$htmlchars=false,$cdata=false)
{
	$this->startElement($element,$vars,false,false);
	$this->charData($data,$cdata,$htmlchars,false);
	$this->endElement($element,false);
}
	
/**
 * Returns XML Buffer
 * @param boolean $header Show an xml header (optional, default NO)
 * @param boolean $html Convert to HTML-friendly output (optional, default NO)
 * @param string $head_version XML Header version if shown (optional, default 1.0)
 * @param string $head_encode XML encoding in header if shown (optional, default utf-8)
 * @return string XML Output
*/
function getXML($header=false,$html=false,$head_version="1.0",$head_encode="utf-8")
{
	$out="";
	if ($header)
		$out="<?xml version=\"".$head_version."\" encoding=\"".$head_encode."\"?>\n";
	$out.=$this->xml;
	if ($html) $out=nl2br(htmlspecialchars($out));
	return $out;
}
	
/**
 * Writes XML Buffer to Screen
 * @param boolean $header Show an xml header (optional, default NO)
 * @param boolean $html Convert to HTML-friendly output (optional, default NO)
 * @param string $head_version XML Header version if shown (optional, default 1.0)
 * @param string $head_encode XML encoding in header if shown (optional, default utf-8)
*/
function echoXML($header=false,$html=false,$head_version="1.0",$head_encode="utf-8")
{
	$out="";
	if ($header) $out.="<?xml version=\"".$head_version."\" encoding=\"".$head_encode."\"?>\n";
	$out.=$this->xml;
	if ($html) $out=nl2br(htmlspecialchars($out));
	echo $out;
}

/**
 * Register ourselves with FreeDESK
 * @param mixed $desk FreeDESK instance
**/
static function Exec(&$desk)
{
	$desk->PluginManager->Register( new Plugin(
		"XML Creator", "0.01", "Core", "XML" ));
}

}
?>
