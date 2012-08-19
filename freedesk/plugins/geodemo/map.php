<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<?php
global $DESK;
$script="http://maps.google.com/maps";
$query="?file=api&v=2&";
$key="AIzaSyBwBvOrBMcnTUEQwGnNNEJhDr5IxlvJg6A";
$uri=$script.$query."key=".$key;
//echo $uri;
//exit();

echo "<script src=\"".$uri."\" type=\"text/javascript\"></script>";

?>
    <script type="text/javascript">

    //<![CDATA[

    function load() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map"));
        var freenats_hq = new GLatLng(52.49, 1.75);
        map.setCenter(new GLatLng(30,28), 2);
        //map.openInfoWindow(point,
        //	document.createTextNode("Dolphin Spas"));
        var greenIcon = new GIcon(G_DEFAULT_ICON);
        greenIcon.image="http://www.google.com/intl/en_us/mapfiles/ms/micons/green-dot.png";
        var yellowIcon = new GIcon(G_DEFAULT_ICON);
        yellowIcon.image="http://www.google.com/intl/en_us/mapfiles/ms/micons/yellow-dot.png";
        var blueIcon = new GIcon(G_DEFAULT_ICON);
        blueIcon.image="http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png";
        var redIcon = new GIcon(G_DEFAULT_ICON);
        redIcon.image="http://www.google.com/intl/en_us/mapfiles/ms/micons/red-dot.png";
        markerOptions={ icon:blueIcon };
        var hqmarker=new GMarker(freenats_hq,markerOptions);
        map.addOverlay(hqmarker);
        hqmarker.bindInfoWindow("FreeNATS HQ");
<?php
$q="SELECT * FROM ".$DESK->Database->Table("vis_country");
$r=$DESK->Database->Query($q);
mt_srand(microtime()*1000000);
while ($row = $DESK->Database->FetchAssoc($r))
{
	$jsv="point_".$row['country'];
	$jsm="marker_".$row['country'];
	echo "var ".$jsv."=new GLatLng(".$row['lat'].",".$row['long'].");\n";
	$count = mt_rand(0,10);
	if ($count < 4)
		$col="green";
	else if ($count < 7)
		$col="yellow";
	else
		$col="red";
	echo "markerOptions={ icon:".$col."Icon };\n";
	echo "var ".$jsm."=new GMarker(".$jsv.",markerOptions);\n";
	echo "map.addOverlay(".$jsm.");\n";
	$cn = str_replace( "\r", "", str_replace("\n","",$row['country_desc']));
	echo $jsm.".bindInfoWindow(\"".$cn.", ".$count." requests\");\n";
}
	

?>
        map.addControl(new GSmallMapControl());
      }
    }

    //]]>
    </script>
  </head>
  <body onload="load()" onunload="GUnload()">
    <div id="map" style="width: 850px; height: 580px"></div>
  </body>
</html>
