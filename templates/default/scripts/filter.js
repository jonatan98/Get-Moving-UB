
/*
 * Script to filter the pins
 */

var activities = [
    {
        id: 1,
        name: "Basketball",
        markers: [0, 3]
    },
    {
        id: 2,
        name: "Ishockey",
        markers: []
    },
    {
        id: 3,
        name: "Volleyball",
        markers: [1]
    },
    {
        id: 4,
        name: "Fotball",
        markers: [2]
    }
];

var areas = [
    {
        id: 1,
        name: "Nordberg",
        markers: [0]
    },
    {
        id: 2,
        name: "Grünerløkka",
        markers: [3]
    },
    {
        id: 3,
        name: "Voldsløkka",
        markers: [1, 2]
    }
];

//Add classes to the markers
for(var i = 0; i < activities.length; i++){
    for(var x = 0; x < activities[i].markers.length; x++){
        //console.log(markers[activities[i].markers[x]]._icon.className += " activity-" + activities[i].id);
    }
}
for(var i = 0; i < areas.length; i++){
    for(var x = 0; x < areas[i].markers.length; x++){
        //console.log(markers[areas[i].markers[x]]._icon.className += " area-" + areas[i].id);
    }
}


//Actually use the filtering
var activity = document.getElementById("activity");
var area = document.getElementById("area");

//Filter arrays
var visible_activity = "";
var visible_area = "";

activity.onchange = function(e){
    if(this.value != ""){
        if(parseInt(this.value, 10) == 0){
            //Display all activities
            visible_activity = "";
            update_filter();
            return;
        }
        //Find the activity data
        for(var i = 0; i < activities.length; i++){ if(parseInt(this.value, 10) == activities[i].id){ break; } }
        if(i == activities.length){
            //Unknown activity
            return;
        }
        
        visible_activity = activities[i].id;
        update_filter();
    }else{
        //Filter is missing ID
        visible_activity = "";
        update_filter();
    }
};

area.onchange = function(e){
    if(this.value != ""){
        if(parseInt(this.value, 10) == 0){
            //Display all areas
            visible_area = "";
            update_filter();
            return;
        }
        //Find the area data
        for(var i = 0; i < areas.length; i++){ if(parseInt(this.value, 10) == areas[i].id){ break; } }
        if(i == areas.length){
            //Unknown area
            return;
        }
        
        visible_area = areas[i].id;
        update_filter();
    }else{
        //Filter is missing ID
        visible_area = "";
        update_filter();
    }
};


function update_filter(){
    var act = ''; var are = '';
    if(visible_activity != ""){
        act = '.activity-' + visible_activity;
    }
    if(visible_area != ""){
        are = '.area-' + visible_area;
    }
    if(visible_area != "" || visible_activity != ""){
        $(".leaflet-marker-icon").hide();
        $(".leaflet-marker-icon" + act + are).show();
    }
    
}