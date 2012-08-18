<?php
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
		echo $userAgent."<br />";
		foreach($mobAgents as $agent)
		{
			if (strpos(	$userAgent, $agent ) !== false)
				return true;
		}
		
		return false;
	}
}

if (BrowserDetect::isMobile())
	echo "Mobile";
else
	echo "Not Mobile";
?>
