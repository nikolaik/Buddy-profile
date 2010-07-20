/* Initialize the map*/
var map;

function initialize() {
        if (GBrowserIsCompatible()) {

                map = new GMap2(document.getElementById("map_canvas"));
                map.setCenter(new GLatLng(60.90, 10.65), 6);
                map.setUIToDefault();
                map.enableScrollWheelZoom();

		loadpoints(0);

        }
        else alert ("Browser not supported");
}



function loadpoints(id){
//      $.ajax({ cache: no }); 
        $.get("../wp-content/plugins/buddy_profile/map.php?type=poi",null,  jsonhandle ,"json");
}

function jsonhandle(data){
        for(i in data){
		alert("hei");
		console.debug(i);
		var point = new GLatLng(data[i].lat, data[i].lon);
        	var marker = null;

        	marker = new GMarker(point, markerOptions);
		map.addOverlay(marker);

        }
}

