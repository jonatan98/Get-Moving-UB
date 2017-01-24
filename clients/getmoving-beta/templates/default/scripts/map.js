
var map;
var center = {lat: Number(coordinates.split(',')[0].trim()), lng: Number(coordinates.split(',')[1].trim())};
var zoom = 14;

function initMap() {
    var mapOptions = {
      zoom: zoom,
      center: center,
      mapTypeId: 'hybrid',
      minZoom: 11
    };
    map = new google.maps.Map(document.getElementById('map'),
        mapOptions);
    
    //Load markers
    for(var i = 0; i < markers.length; i++){
        addMarker(i, markers[i]);
    }
    
    //Set max zoom level
}

/*
 * Center the map around the user
 */

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position){
        //position.coords.latitude
        //position.coords.longitude
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
    active: iconBase + 'pin v2.active.min.png',
    inactive: iconBase + 'pin v2.min.png'
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
    
    var active_users_length = markerData.active_users.length;
    var soon_active_users_length = markerData.soon_active_users.length;
    var active_user = null;
    var soon_active_user = false;
    var cancel_date_link = '';
    
    //Check if the user is active at the given location
    for(var z = 0; z < active_users_length; z++){
        if(markerData.active_users[z].id == user.id){
            active_user = markerData.active_users[z];
            cancel_data_link = '<a href="javascript:form_leave(' + markerIndex + ', \'' + active_user.start_time + '\')">Jeg har dratt</a>';
            break;
        }
    }
    //Get a formatted string with the nr of active users
    var activeUsers = getActiveUsersString(active_users_length, active_user != null);
    
    //Fetch all the times the users are leaving
    var leaves = [];
    for(var i = 0; i < active_users_length; i++){
        leaves.push(markerData.active_users[i].stop_time);
    }
    var leave_string = '';
    if(active_users_length > 0){
        var leave_string = '(drar ' + leaves.join(', ') + ')';
    }
    
    //Get the amount of soon active users
    var soonActiveUsers = "";
    if(soon_active_users_length > 0){
        //Check if the user is active at the given location
        for(var z = 0; z < soon_active_users_length; z++){
            if(markerData.soon_active_users[z].id == user.id){
                active_user = markerData.soon_active_users[z];
                soon_active_user = true;
                cancel_data_link = '<a href="javascript:form_cancel(' + markerIndex + ', \'' + active_user.start_time + '\')">Jeg kommer ikke</a>';
                break;
            }
        }
        var soonActiveUsers = getSoonActiveUsersString(soon_active_users_length, active_user != null);
    }
    
    //Fetch all the times the users are arriving
    var arrives = [];
    for(var i = 0; i < soon_active_users_length; i++){
        arrives.push(markerData.soon_active_users[i].stop_time);
    }
    var arrive_string = '';
    if(soon_active_users_length > 0){
        var arrive_string = '(kommer ' + arrives.join(', ') + ')';
    }
    
    //Format infoWindowContent
    var infoWindowContent = '<h3>' + markerData.name + '</h3>' +
       '<p>' + markerData.description + '</p>' +
       '<p>' + activeUsers + ' ' + leave_string + '</p>' +
       '<p>' + soonActiveUsers + ' ' + arrive_string + '</p>' +
       '<p>Logg inn for å se hvem de er eller <br>si at du er her.</p>';
    if(user.id != 0){
        if(active_user != null){
            //User is active at the current location
            infoWindowContent = '<h3>' + markerData.name + '</h3>' +
               '<p>' + markerData.description + '</p>' +
               '<p>' + activeUsers + ' ' + leave_string + '</p>' +
               '<p>' + soonActiveUsers + ' ' + arrive_string + '</p>' +
               '<p class="user-links"><a href="javascript:form_activeuser(' + markerIndex + ', \'' + active_user.start_time + '\', \'' + active_user.stop_time + '\')">Endre dratidspunkt</a>' + cancel_data_link + '</p>';
        }else{
            //User is not active at the current location
            infoWindowContent = '<h3>' + markerData.name + '</h3>' +
               '<p>' + markerData.description + '</p>' +
               '<p>' + activeUsers + ' ' + leave_string + '</p>' +
               '<p>' + soonActiveUsers + ' ' + arrive_string + '</p>' +
               '<p class="user-links"><a href="javascript:form_newuser(' + markerIndex + ', true)">Jeg er her</a><a href="javascript:form_newuser(' + markerIndex + ', 0)">Jeg skal hit</a></p>';
        }
    }
    
    infoWindowContent = '<div class="info-window">' + infoWindowContent + '</div>';
    
    var infoWindow = new google.maps.InfoWindow({
          content: infoWindowContent
    });
    infoWindows[infoWindows.length] = infoWindow;
    
    //Display marker on click
    marker.addListener("click", function(){
        //Hide all other infoWindows
        for(var x = 0; x < infoWindows.length; x++){
            infoWindows[x].close();
        }
        //Display this infoWindow
        infoWindow.open(map, marker);
    });
    
    //Display marker on hover
    marker.addListener('mouseover', function() {
        //Do not display infoWindow on hover if the map is too zoomed out
        if(map.getZoom() < 15){
            return;
        }
        //Hide all other infoWindows
        for(var x = 0; x < infoWindows.length; x++){
            infoWindows[x].close();
        }
        //Display this infoWindow
        infoWindow.open(map, this);
    });

    /*marker.addListener('mouseout', function() {
        infoWindow.close();
    });*/
    
    //Store marker data
    markers[markerIndex].marker = marker;
}

function getActiveUsersString(n, user_is_active){
    if(user_is_active){
        n--;
        switch(n){
            case 0: return 'Du er den eneste aktive brukeren';
            case 1: return 'Du, og 1 annen aktiv bruker';
            default: return 'Du, og ' + n + ' andre aktive brukere';
        }
    }else{
        switch(n){
            case 0: return 'Ingen aktive brukere';
            case 1: return '1 aktiv bruker';
            default: return n + ' aktive brukere';
        }
    }
}
function getSoonActiveUsersString(n, user_is_active){
    if(user_is_active){
        n--;
        switch(n){
            case 0: return 'Du kommer snart';
            case 1: return 'Du, og 1 annen bruker kommer snart';
            default: return 'Du, og ' + n + ' andre brukere kommer snart';
        }
    }else{
        switch(n){
            case 1: return '1 bruker kommer snart';
            default: return n + ' brukere kommer snart';
        }
    }
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
