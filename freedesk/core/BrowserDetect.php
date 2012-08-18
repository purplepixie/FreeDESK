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
 * Browser detection
**/
class BrowserDetect
{
	/**
	 * Check if the browser is mobile
	 * @return bool True if we think it is
	**/
	static function isMobile()
	{
		$mobAgents = array(
			"sony", "symbian", "nokia", "samsung", "mobile", "windows ce",
			"epoc", "opera mini", "nitro", "j2me", "midp-", "cldc-",
			"netfront", "mot", "up.browser", "up.link", "audiovox",
			"blackberry", "ericsson", "philips", "sanyo", "sharp",
			"series60", "palm", "pocketpc", "smartphone", "rover", "ipaq",
			"alcatel", "vodafone/", "wap1.", "wap2.", "android");
		$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
		foreach($mobAgents as $agent)
		{
			if (strpos(	$userAgent, $agent ) !== false)
				return true;
		}
		
		return false;
	}
}
?>			
