/* Initialize the map*/
var map;

function initialize() {
        if (GBrowserIsCompatible()) {

                map = new GMap2(document.getElementById("map_canvas"));
                map.setCenter(new GLatLng(59.94257,10.718014), 15);
                map.setUIToDefault();
                map.enableScrollWheelZoom();
		map.setMapType(G_SATELLITE_MAP);

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

		create_marker(data[i]);
       }
}

function create_marker(info){
		var p = null;
		p = new Point(1, info.lat, info.lon, info.navn, info.beskrivelse);
		p.point = new GLatLng(p.lat, p.lon);

        	p.marker  = new GMarker(p.point);
		GEvent.addListener(p.marker, "click", function() {  
			p.marker.openInfoWindow(p.text);  
		});

		map.addOverlay(p.marker);
 
}

function Point(id, lat, lon, text){
        this.id = id;
        this.lat = lat;
        this.lon = lon;
	this.text = text;
	this.beskrivelse;
	this.marker = null;
	this.point = null;
}

