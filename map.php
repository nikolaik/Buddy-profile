<?php

require_once('../../../wp-config.php');

$connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
$db = mysql_select_db("cyb") or die(mysql_error());

$json=array();

if ($_GET["type"] == "poi") {
	$res = mysql_query("SELECT * from fad_poi") or die(mysql_error());
	while($row = mysql_fetch_array($res))
	{
	array_push($json, $row);
	}
}
 
mysql_close($connection);
die(json_encode($json));
?>
