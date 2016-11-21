
/*
 * Script to filter the pins
 */

var activities = [
    {
        id: 1,
        name: "Basketball",
        markers: [0, 1, 2]
    },
    {
        id: 2,
        name: "Ishockey",
        markers: [0,3,4]
    }
];

//Add classes to the markers
for(var i = 0; i < activities.length; i++){
    for(var x = 0; x < activities[i].markers.length; x++){
        console.log(markers[activities[i].markers[x]]._icon.className += " activity-" + activities[i].id);
    }
}


//Actually use the filtering
var activity = document.getElementById("activity");

activity.onchange = function(e){
    if(this.value != ""){
        if(parseInt(this.value, 10) == 0){
            //Display all activities
            $(".leaflet-marker-icon").show();
            return;
        }
        //Find the activity data
        for(var i = 0; i < activities.length; i++){ if(parseInt(this.value, 10) == activities[i].id){ break; } }
        if(i == activities.length){
            //Unknown activity
            return;
        }
        
        $(".leaflet-marker-icon").hide();
        $(".activity-" + activities[i].id).show();
    }else{
        //Filter is missing ID
    }
};