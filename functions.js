/* Initialize the map*/
var map;
var bounds;
var persons=new Array();
var poi=new Array();
var man=new Array();
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


		man[0] = new GIcon(G_DEFAULT_ICON);
		man[0].image = "http://folk.uio.no/mariusno/man-g.png";
		man[0].iconSize = new GSize(38, 38);

		man[1] = new GIcon(G_DEFAULT_ICON);
		man[1].image = "http://folk.uio.no/mariusno/man-y.png";
		man[1].iconSize = new GSize(38, 38);

		man[2] = new GIcon(G_DEFAULT_ICON);
		man[2].image = "http://folk.uio.no/mariusno/man-r.png";
		man[2].iconSize = new GSize(38, 38);
	        }
        else alert ("Browser not supported");
}
 var poly = [] ; 
 var line ; 
 


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
	map.setZoom(map.getBoundsZoomLevel(bounds)-1);
	if (high != ""){
		highlight(high);
	}


}


function poihandle(data){
        for(i in data){0
		create_poi(data[i]);
       }
	map.setCenter(bounds.getCenter());
	map.setZoom(map.getBoundsZoomLevel(bounds));


}

function create_poi(info){
		var p = null;
		p = new Point(1, info.lat, info.lon, info.navn, info.beskrivelse);
		poi[info.p_id] = p;
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
		p = new Person(info.user_id, info.lat, info.lon, info.time, info.acc);
		persons[info.user_id] = p;
		p.point = new GLatLng(p.lat, p.lon);
		bounds.extend(p.point);

		var foo = new Date; // Generic JS date object
		var unixtime_ms = foo.getTime(); // Returns milliseconds since the epoch
		var now = parseInt(unixtime_ms / 1000);

		var index;
		if (p.time > now - (60*60*1))  index = 0;
		else if ((p.time < now - (60*60*1)) && (p.time > now - (60*60*5)))  index = 1;
		else index = 2;

        	p.marker  = new GMarker(p.point, { icon: man[index]});
		GEvent.addListener(p.marker, "click", function() {  
			selectMarker(p.id);
		});

		map.addOverlay(p.marker);
 
}

function highlight(id){
	if (persons[id] == null);
	selectMarker(id);
}

function getText(id){
	var text = $("#ui"+id).html();
	return text;	
}

function selectMarker(id){
	persons[id].marker.openInfoWindow(getText(id));  
	map.setZoom(15);
	var acc = persons[id].acc / 1609.344;
	drawCircle(map, persons[id].point, acc, "40");

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

function Person(id, lat, lon, time, acc){
        this.id = id;
        this.lat = lat;
        this.lon = lon;
	this.time = time;
	this.beskrivelse;
	this.acc=acc;
	this.marker = null;
	this.point = null;
}



        // Draw a circle on map around center (radius in miles)
        // Modified by Jeremy Schneider based on http://maps.huge.info/dragcircle2.htm
        function drawCircle(map, center, radius, numPoints)
        {
            poly = [] ; 
            var lat = center.lat() ;
            var lng = center.lng() ;
            var d2r = Math.PI/180 ;                // degrees to radians
            var r2d = 180/Math.PI ;                // radians to degrees
            var Clat = (radius/3963) * r2d ;      //  using 3963 as earth's radius
            var Clng = Clat/Math.cos(lat*d2r);
            
            //Add each point in the circle
            for (var i = 0 ; i < numPoints ; i++)
            {
                var theta = Math.PI * (i / (numPoints / 2)) ;
                Cx = lng + (Clng * Math.cos(theta)) ;
                Cy = lat + (Clat * Math.sin(theta)) ;
                poly.push(new GLatLng(Cy,Cx)) ;
            }
 
            //Remove the old line if it exists
            if(line)
            {
                map.removeOverlay(line) ;
            }
            
            //Add the first point to complete the circle
            poly.push(poly[0]) ;
 
            //Create a line with teh points from poly, red, 3 pixels wide, 80% opaque
            line = new GPolyline(poly,'#FF0000', 3, 0.8) ;
            
            map.addOverlay(line) ;
        }

