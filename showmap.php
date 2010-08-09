<?php
function getMarker($time){
	$var = date(U)-$time;
	if ($var <= 60*60*1) return '<img src="http://folk.uio.no/mariusno/man-g.png"/>';
	else if ($var > 60*60*1 && $var < 60*60*5) return '<img src="http://folk.uio.no/mariusno/man-y.png"/>';
	else return '<img src="http://folk.uio.no/mariusno/man-r.png"/>';
}

function unixToNormal($time){
	setlocale(LC_ALL, 'no_NO');

	date_default_timezone_set('Europe/Oslo');
	return strftime('%Y-%m-%d %H:%M:%S', $time);
}

function draw_maps(){
	print '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAArmJmbq77m8lzUfEaPb2ZvhRH0MZJ1dk1u0j8Vy0StsBioyX4SBRDkuc0WPtU5ftZOtCDjxUJ7nZPbw"   type="text/javascript"></script>
<script src="../wp-content/plugins/buddy_profile/functions.js" type="text/javascript"></script>
<link href="../wp-content/plugins/buddy_profile/style.css" rel="stylesheet" type="text/css"></link>


	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>


<div id="map">
	<div id="mapi">
		<div id="map_canvas"></div>
    		<div id="placeholder"></div>
	</div>
</div>

';

if ($_GET['type'] == "persons"){

	global $wpdb;
	$res = $wpdb->get_results("SELECT * from fad_users natural join 
	(select user_id AS ID, meta_value from fad_usermeta where meta_key like 'fad_geotime') AS b order by meta_value DESC");
	    foreach($res as $person) { 
		$per = get_userdata($person->ID);
		if (!isset($per->fad_geotime) && $per->fad_geotime != "") continue;
		if (!isset($per->fad_geoacc)) continue;
		$jcode = "<a href=\"javascript:selectMarker(".$per->ID.");\">";
		?>
		<div id="ui<? echo$per->ID?>" style="visibility:hidden; height:0px">
		<?php echo get_bilde($per->ID)?><?php echo $per->first_name." (".$per->nickname.")<br/>".unixToNormal($per->fad_geotime);?>
		</div>
		<?
		$marker = getMarker($per->fad_geotime);
		echo $marker.$jcode.$per->nickname.'</a><br/>';
print '
<script  type="text/javascript">
	$(function() { initialize("'.$_GET['type'].'","'.$_GET['highlight'].'"); });

</script>
';	


    	}


}
else{
	global $wpdb;
	$res = $wpdb->get_results("SELECT * from fad_poi");
	    foreach($res as $poi) { 
//		echo $poi->navn.'<br/>';
		$jcode = "<a href=\"javascript:poi[".$poi->p_id."].marker.openInfoWindow('".$poi->navn."');\">";
		echo $jcode.$poi->navn.'</a><br/>';
print '<script  type="text/javascript">
	$(function() { initialize("'.$_GET['type'].'","'.$_GET['highlight'].'"); });

</script>';
'


    	}
}
}
?>
