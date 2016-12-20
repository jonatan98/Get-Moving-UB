
var map;
var center = {lat: Number(coordinates.split(',')[0].trim()), lng: Number(coordinates.split(',')[1].trim())};
var zoom = 14;

function initMap() {
    var mapOptions = {
      zoom: zoom,
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
        console.log(position.coords.latitude + " " + position.coords.longitude);
        if(map == null){
            center = {lat: position.coords.latitude, lng: position.coords.longitude};
            zoom = 15;
        }else{
            map.setCenter(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
            map.setZoom(15);
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
          content: '<h3>' + markerData.name + '</h3>' +
                   '<p>' + markerData.description + '</p>' +
                   '<p>' + markerData.active_users + ' aktive brukere</p>' +
                   '<p>Logg inn for å se hvem de er eller <br>si at du er her.</p>'
    });
    infoWindows[infoWindows.length] = infoWindow;
    
    //Display marker on click
    marker.addListener("click", function(){
        for(var x = 0; x < infoWindows.length; x++){
            infoWindows[x].close();
        }
        infoWindow.open(map, marker);
    });
    
    //Display marker on hover
    marker.addListener('mouseover', function() {
        for(var x = 0; x < infoWindows.length; x++){
            infoWindows[x].close();
        }
        infoWindow.open(map, this);
    });

    /*marker.addListener('mouseout', function() {
        infoWindow.close();
    });*/
    
    //Store marker data
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
