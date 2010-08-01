<?php
function move_maps($fu){
include("Mobile_Detect.php"); 
$detect = new Mobile_Detect;
global $current_user;
global $wpdb;
$hei = "";
if ($current_user->ID == ""){
	//if (isset($_GET["mobil"])) $hei = "mobil=".$_GET["mobil"];
	print '<meta http-equiv="refresh" content="0;url=../wp-login.php?redirect_to=http://cyb.ifi.uio.no/fadderuke/movemarke/" />';
	//print '<meta http-equiv="refresh" content="0;url=../wp-login.php?redirect_to=http://cyb.ifi.uio.no/fadderuke/movemarke/?'.$hei.'" />';
	//wp_redirect(admin_url("wp-login.php?redirect_to=http://cyb.ifi.uio.no/fadderuke/movemarke/"));
}
else if(isset($_POST["latitude"])){
	update_usermeta( $current_user->ID, 'fad_lat', $_POST['latitude'] );
	update_usermeta( $current_user->ID, 'fad_lon', $_POST['longitude'] );
	update_usermeta( $current_user->ID, 'fad_geotime', $_POST['time'] );
	update_usermeta( $current_user->ID, 'fad_geoacc', $_POST['acc'] );
}

else{

?>
<div id="up"> </div>
<div id="map" style="width: 600px; height: 420px"></div>
<!-- add your map api key here -->
<script type="text/javascript" src="http://maps.google.com/maps?file=api&v=2&key=ABQIAAAArmJmbq77m8lzUfEaPb2ZvhRH0MZJ1dk1u0j8Vy0StsBioyX4SBRDkuc0WPtU5ftZOtCDjxUJ7nZPbw"></script>

<script type="text/javascript">	
<!--
if (GBrowserIsCompatible()) 
{
	// create map and add controls
	var map = new GMap2(document.getElementById("map"));
	map.addControl(new GLargeMapControl());        
	map.addControl(new GMapTypeControl());
	
	// set centre point of map
	var centrePoint = new GLatLng('<?= ($current_user->fad_lat != 0) ? $current_user->fad_lat : "59.12"  ?>', '<?= ($current_user->fad_lon != 0) ? $current_user->fad_lon : "10.12"  ?>');
	map.setCenter(centrePoint, 14);	
	
	// add a draggable marker
	var marker = new GMarker(centrePoint, {draggable: true});
	map.addOverlay(marker);
	
	// add a drag listener to the map
	GEvent.addListener(marker, "dragend", function() {
		var point = marker.getPoint();
		map.panTo(point);
		var foo = new Date; // Generic JS date object
		var unixtime_ms = foo.getTime(); // Returns milliseconds since the epoch
		var now = parseInt(unixtime_ms / 1000);


//		document.getElementById("latitude").value = point.lat();
//		document.getElementById("longitude").value = point.lng();
//		document.getElementById("time").value = now;
		
		var pointers = {
			"latitude": point.lat(),
			"longitude": point.lng(),
			"time": now,
			"acc": "10"
		};


		$.post('http://cyb.ifi.uio.no/fadderuke/movemarke/', pointers, function(data){
		     $("#up").html("Oppdatert: "+now);
		});
    }); //listener
<?php 
if ($detect->isMobile() || $_GET["mobil"] == "y"){ 
?>
var trails;
var watchID;
function positionHandler(location) {
			/* set a global val for later submition. */
			loc = location;
		/*	var googlestaticurl = "http://maps.google.com/staticmap?center=" + location.coords.latitude + "," + location.coords.longitude + "&size=280x210&maptype=hybrid&zoom=16&key=" + apikey;
			message.innerHTML ="<a href='" + googlestaticurl +"'><img src='" + googlestaticurl +"' /></a>";
			message.innerHTML+="<p>Longitude: " + location.coords.longitude + "</p>";
			message.innerHTML+="<p>Latitude: " + location.coords.latitude + "</p>";
			message.innerHTML+="<p>Accuracy: " + location.coords.accuracy + " meters</p>";
			message.innerHTML+="<p>Timestamp: " + location.timestamp.toTimeString() + "</p>";
			message.innerHTML+="<p>Your might know where <span id='streetaddr'>...</span> is?</p>";*/
			/* Note: Call it here, because streetaddr has to exist in the dom first. */
			/*codeLatLng(location.coords.latitude, location.coords.longitude);
			button.innerHTML = "<input type='button' value='Update location' onclick='updateDBWithLocation();' />";*/

		if (location.coords.accuracy <= 300) navigator.geolocation.clearWatch(watchID);
		if (location.coords.accuracy > 300) return;

		updateDBWithLocation()
}	
function positionGearsHandler(location) {
			loc = location;
		if (location.coords.accuracy > 300){
			$("#up").append("<input type='button' value='Retry' onclick='geo.getCurrentPosition(positionGearsHandler, errorHandler);' />");
			return;
		}
		updateDBWithLocation()
}	



function positionHighResHandler(location) {
		loc = location;

		if (location.coords.accuracy <= 30) navigator.geolocation.clearWatch(watchID);
		if (location.coords.accuracy > 30) return;

		updateDBWithLocation()
}
function stopHigh(){
	navigator.geolocation.clearWatch(watchID);

}


function updateH() {	
	watchID = navigator.geolocation.watchPosition(positionHighResHandler, errorHigh, {enableHighAccuracy:true, timeout:40000});
		$("#up").html("Prøver å få så godt koordinat som mulig.");
		$("#up").append("<input type='button' value='Stop' onclick='stopHigh();' />");
}
		
		function errorHandler(locationError) {
			$("#up").append("Fungerte ikke");
		}

		function errorHigh(locationError) {
			$("#up").html("Feil ved høykvalitets koordinat, prøver lav");
			navigator.geolocation.clearWatch(watchID);
			navigator.geolocation.getCurrentPosition(positionHandler, errorHandler);
		}
		function errorLow(locationError) {
			$("#up").html("Feil ved lavkvalitets koordinat, prøve igjen");
		}

	
		function updateDBWithLocation() {
			
			
			
			var cur = new GLatLng(loc.coords.latitude, loc.coords.longitude);
			map.setCenter(cur, 14);	
			var ico = new GIcon(G_DEFAULT_ICON);
			ico.image = "http://maps.google.com/mapfiles/ms/micons/phone.png";
			ico.iconSize = new GSize(38, 38);
			var cm = new GMarker(cur, {icon: ico, draggable: false});
			map.addOverlay(cm);
	
			/* Do update */
			var foo = new Date; // Generic JS date object
			var unixtime_ms = foo.getTime(); // Returns milliseconds since the epoch
			var now = parseInt(unixtime_ms / 1000);
			var pointers = {
				"latitude": loc.coords.latitude,
				"longitude": loc.coords.longitude,
				"time": now,
				"acc": loc.coords.accuracy
			};
			$.post('http://cyb.ifi.uio.no/fadderuke/movemarke/', pointers, function(data){
			     $("#up").html("Oppdatert: "+now+ " Av med "+loc.coords.accuracy+"m");
				$("#up").append("<input type='button' value='Try higher acc' onclick='updateH();' />");
			});
		}
		var ret;
		function retry(){
		navigator.geolocation.getCurrentPosition(positionHandler, errorHigh, {enableHighAccuracy:true, timeout:40000, maximumAge: 1000});
		}

		/* Note: 'positionHandler' is the callback-function which puts Position-object in 'location'. */
		if(navigator.geolocation) {
			$("#up").html("Søker etter posisjon");
//			navigator.geolocation.getCurrentPosition(positionHandler, errorHigh, {enableHighAccuracy:true});
			watchID = navigator.geolocation.watchPosition(positionHandler, errorHigh, {enableHighAccuracy:true, timeout:20000});
		}
		else { 
			/* Note: This is for older versions of Android. */
			try {
				var geo = google.gears.factory.create('beta.geolocation');
				geo.getCurrentPosition(positionGearsHandler, errorHandler);
			}
			catch(error) {
				$("#up").html("Traff catch error, ikke mulig med mobil geolocation. Dra markeren istedet");
			
			}
		}
<?php
}
?>


}
//-->
</script>
<?php
}}
?>
