<?php
if (isset($data['id']))
	$id = $data['id'];
else
	$id = "pane";

echo "<div id=\"pane_".$id."\" class=\"pane_set\">\n";
echo "<div id=\"pane_".$id."_header\" class=\"pane_header\">\n";

$first=true;
foreach($data['panes'] as $pid => $pane)
{
	if ($first)
	{
		$first=false;
		$class="pane_option_selected";
	}
	else
		$class="pane_option";
	echo "<span id=\"pane_".$id."_".$pid."\" class=\"".$class."\">\n";
	echo "<a href=\"#\" onclick=\"DESK.paneSwitch('".$id."','".$pid."');\">";
	echo $pane['title'];
	echo "</a></span>\n";
}

echo "</div>\n";

echo "<div id=\"pane_".$id."_content\" class=\"pane_content\">\n";
?>
