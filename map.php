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

if ($_GET["type"] == "persons") {
	$res = mysql_query("select * from (SELECT `user_id`,`meta_value` as lat FROM `fad_usermeta` WHERE `meta_key` like 'fad_lat' ) as elat  NATURAL JOIN (SELECT `user_id`,`meta_value` as lon FROM `fad_usermeta` WHERE `meta_key` like 'fad_lon' ) as elon NATURAL JOIN (SELECT `user_id`,`meta_value` as time FROM `fad_usermeta` WHERE `meta_key` like 'fad_geotime' ) as etime NATURAL JOIN (SELECT `user_id`    ,`meta_value` as acc FROM `fad_usermeta` WHERE `meta_key` like 'fad_geoacc' ) as eacc") or die(mysql_error());
	while($row = mysql_fetch_array($res))
	{
	array_push($json, $row);
	}

}
 
mysql_close($connection);
//var_dump($json);
die(json_encode($json));
?>
