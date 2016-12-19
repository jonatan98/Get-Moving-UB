
/*
 * Script to filter the pins
 */

var filter = {
    _activity: 0,
    _area: 0
};


//Setup the listeners

document.getElementById("activity").onchange = function(e){
    filter._activity = parseInt(this.value, 10);
    update_filter();
};

document.getElementById("area").onchange = function(e){
    filter._area = parseInt(this.value, 10);
    update_filter();
};


function update_filter(){
    for(var i = 0; i < markers.length; i++){
        if(
            ($.inArray( filter._area, markers[i].areas ) != -1 || filter._area == 0)
            &&
            ($.inArray( filter._activity, markers[i].activities ) != -1 || filter._activity == 0)
        ){
            markers[i].marker.setMap(map);
        }else{
            markers[i].marker.setMap(null);
        }
    }
}