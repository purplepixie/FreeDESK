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
?>
<div id="login_form" class="login_form">
<div id="login_header" class="login_header">
<?php echo $DESK->Lang->Get("login"); ?>
</div>
<div id="login_content" class="login_content">
<div class="login_title"><?php echo $DESK->Lang->Get("username"); ?></div>
<div class="login_field"><input type="text" name="username" size="15" id="login_username" /></div>
<br />
<div class="login_title"><?php echo $DESK->Lang->Get("password"); ?></div>
<div class="login_field"><input type="password" name="password" size="15" id="login_password" /></div>
<br /><br />
<script type="text/javascript">
function login_action(responseXML)
{
	//alert(responseXML);
	var txt = document.createTextNode(responseXML);
	document.getElementById("login_content").appendChild(txt);
}

function login_click()
{
	var req = new ServerRequest();
	req.url = "api.php?mode=login&type=user&username="
		+document.getElementById("login_username").value
		+"&password="
		+document.getElementById("login_password").value;
	req.callback = login_action;
	req.Get();
}
</script>
<div class="login_button"><input type="submit" value="<?php echo $DESK->Lang->Get("login"); ?>" onclick="login_click()" /></div>
<?php
if (isset($data['errorflag']))
{
	echo "<div class=\"error\">".$DESK->Lang->Get("login_fail")."</div>";
}
?>
</div>
<div id="login_footer" class="login_footer"></div>
</div>

