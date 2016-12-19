
var map;
var center = {lat: Number(coordinates.split(',')[0].trim()), lng: Number(coordinates.split(',')[1].trim())};
function initMap() {
    var mapOptions = {
      zoom: 14,
      center: center,
      mapTypeId: 'hybrid'
    };
    map = new google.maps.Map(document.getElementById('map'),
        mapOptions);
    
    //Load markers
    for(var i = 0; i < markers.length; i++){
        addMarker(i, markers[i]);
    }
    
    
          
    /*var marker = new google.maps.Marker({
        position: center,
        map: map,
        title: 'Addresse',
        labelContent: "$425K",
        labelAnchor: new google.maps.Point(22, 0),
        labelClass: "labels", // the CSS class for the label
        labelStyle: {opacity: 0.75}
    });*/
    
    /*google.maps.event.addListener(map, 'click', function( event ){
        console.log( "Latitude: "+event.latLng.lat()+" "+", longitude: "+event.latLng.lng() ); 
    });*/
}

/*
 * Center the map around the user
 */

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position){
        //position.coords.latitude
        //position.coords.longitude
        if(map == null){
            center = {lat: position.coords.latitude, lng: position.coords.longitude}
        }else{
            map.setCenter(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
        }
    });
} else {
    // = "Geolocation is not supported by this browser.";
}

/*
 * Map markers
 */

var iconBase = static_url + 'imgs/map/';
var icons = {
    normal: iconBase + 'pin v2.min.png'
};

var infoWindows = [];

function addMarker(markerIndex, markerData){
    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(markerData.position[0], markerData.position[1]),
        icon: new google.maps.MarkerImage(
            icons[markerData.type],
            null, //size is determined at runtime
            null, // origin is 0,0
            null, // anchor is bottom center of the scaled image
            new google.maps.Size(30, 48)
        ),
        map: map
    });
    
    var infoWindow = new google.maps.InfoWindow({
          content: markerData.info
    });
    infoWindows[infoWindows.length] = infoWindow;
    
    marker.addListener("click", function(){
        for(var x = 0; x < infoWindows.length; x++){
            infoWindows[x].close();
        }
        infoWindow.open(map, marker);
    });
    markers[markerIndex].marker = marker;
}

/*
 * Map settings
 */

document.getElementById("updateMapType").addEventListener("change", function(e){
    switch(this.value){
        case "roadmap":
        case "satellite":
        case "hybrid":
        case "terrain":
            map.setMapTypeId(this.value);
    } 
});

/*
 * Leaflet
 */

//Get the user's position
/*var x = document.getElementById("demo");
var pos = [59.929904118285846, 10.754928588867188];
var zoom = 13;

var map = L.map('map', {zoomControl: false}).setView(pos, zoom);
L.tileLayer('https://opencache.statkart.no/gatekeeper/gk/gk.open_gmaps?layers=norges_grunnkart&zoom={z}&x={x}&y={y}').addTo(map);

//add zoom control with your options
L.control.zoom({
     position:'topleft'
}).addTo(map);

//Init custom marker
var gmIcon = L.icon({
    iconUrl: 'imgs/Get Moving pin v2.png',
    //shadowUrl: 'leaf-shadow.png',

    iconSize:     [25, 40], // size of the icon
    //shadowSize:   [50, 64], // size of the shadow
    iconAnchor:   [12.5, 40], // point of the icon which will correspond to marker's location
    shadowAnchor: [4, 40],  // the same for the shadow
    popupAnchor:  [0, -42] // point from which the popup should open relative to the iconAnchor
});


//Display marker
var markers = [
    L.marker([59.96251369439251, 10.73164701461792], {icon: gmIcon}).addTo(map).bindPopup("Nordberg Skole Basket"),
    L.marker([59.9438244699759, 10.752203464508058], {icon: gmIcon}).addTo(map).bindPopup("Voldsløkka sandvolleyball"),
    L.marker([59.94372774638071, 10.750722885131838], {icon: gmIcon}).addTo(map).bindPopup("Voldsløkka kunstgress"),
    L.marker([59.923224037664475, 10.75514316558838], {icon: gmIcon}).addTo(map).bindPopup("Kubaparken (Grünerløkka)"),
    L.marker([59.87636118779293, 10.690383911132812], {icon: gmIcon}).addTo(map).bindPopup("Prima badeplass")
];*/

/* Find coordinates on click

map.on('click', function(e) {
    console.log("[" + e.latlng.lat + ", " + e.latlng.lng + "]")
});
*/