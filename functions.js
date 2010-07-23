/* Initialize the map*/
var map;
var bounds;
var persons=new Array();
var high;
function initialize(type,inhigh) {
        if (GBrowserIsCompatible()) {

                map = new GMap2(document.getElementById("map_canvas"));
                map.setCenter(new GLatLng(59.94257,10.718014), 15);
                map.setUIToDefault();
                map.enableScrollWheelZoom();

		bounds = new GLatLngBounds();
		high = inhigh;
		if (type == "persons") loadpers(0);
		else if (type == "poi") loadpoi(0);
		else loadpoi(0);
	        }
        else alert ("Browser not supported");
}



function loadpoi(id){
	map.setMapType(G_HYBRID_MAP);
//      $.ajax({ cache: no }); 
        $.get("../wp-content/plugins/buddy_profile/map.php?type=poi",null,  poihandle ,"json");
}

function loadpers(id){
	map.setMapType(G_NORMAL_MAP);
	$.get("../wp-content/plugins/buddy_profile/map.php?type=persons",null,  personhandle ,"json");
}

function personhandle(data){
        for(i in data){
		create_person(data[i]);
       	}
       	map.setCenter(bounds.getCenter());
	map.setZoom(map.getBoundsZoomLevel(bounds));
	if (high != ""){
		highlight(high);
	}


}



function poihandle(data){
        for(i in data){
		create_poi(data[i]);
       }
	map.setCenter(bounds.getCenter());
	map.setZoom(map.getBoundsZoomLevel(bounds));


}

function create_poi(info){
		var p = null;
		p = new Point(1, info.lat, info.lon, info.navn, info.beskrivelse);
		p.point = new GLatLng(p.lat, p.lon);
		bounds.extend(p.point);

        	p.marker  = new GMarker(p.point);
		GEvent.addListener(p.marker, "click", function() {  
			p.marker.openInfoWindow(p.text);  
		});

		map.addOverlay(p.marker);
 
}

function create_person(info){
		var p = null;
		p = new Person(info.user_id, info.lat, info.lon, info.time);
		persons[info.user_id] = p;
		p.point = new GLatLng(p.lat, p.lon);
		bounds.extend(p.point);

        	p.marker  = new GMarker(p.point);
		GEvent.addListener(p.marker, "click", function() {  
			p.marker.openInfoWindow(p.time);  
		});

		map.addOverlay(p.marker);
 
}

function highlight(id){
	if (persons[id] == null);
	persons[id].marker.openInfoWindow(persons[id].time);
}

function Point(id, lat, lon, text, beskrivelse){
        this.id = id;
        this.lat = lat;
        this.lon = lon;
	this.text = text;
	this.beskrivelse = beskrivelse;
	this.marker = null;
	this.point = null;
}

function Person(id, lat, lon, time){
        this.id = id;
        this.lat = lat;
        this.lon = lon;
	this.time = time;
	this.beskrivelse;
	this.marker = null;
	this.point = null;
}

