<?php
require("../freedesk/core/FreeDESK.php");
$DESK = new FreeDESK("../freedesk/");
$DESK->Start();

$to="tester@purplepixie.org";
$id=3;

$sub = "Test Message";
$body = "Hello World";

try
{
	if ($DESK->Email->Send($id, $to, $sub, $body))
		echo "Success\n";
	else
		echo "Failure\n";
}
catch(phpmailerException $e)
{
	echo $e->errorMessage();
}

?>
