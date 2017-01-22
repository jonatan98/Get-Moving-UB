
//Hide form cover
function hide_form_cover(){
    document.getElementById("form_cover").style.display = "none";
    hide_popups();
}

//Prompt the user with a form questioning when and for how long it will be there
function form_newuser(markerIndex, ishere){
    hide_popups();
    document.getElementById("form_cover").style.display = "block";
    var div = document.getElementById("form_timepicker");
    
    div.getElementsByClassName("locationID")[0].value = markers[markerIndex].id;
    //Show start time
    if(ishere){
        div.getElementsByClassName("arrival_div")[0].style.display = "none";
    }else{
        div.getElementsByClassName("arrival_div")[0].style.display = "block";
    }
    
    var now = new Date();
    if(!ishere){
        div.getElementsByClassName("arrival_time")[0].value = pad(now.getHours()) + ":" + pad(now.getMinutes());
    }
    var sooner = new Date(now.setHours((now).getHours()+2));
    div.getElementsByClassName("leave_time")[0].value = pad(sooner.getHours()) + ":" + pad(sooner.getMinutes());
    
    div.style.display = "block";
}

//Prompt the user with a form requesting new data for arrival and leaving
function form_activeuser(markerIndex, start_time, stop_time){
    //TODO display form
    hide_popups();
    document.getElementById("form_cover").style.display = "block";
    var div = document.getElementById("form_timepicker");
    
    div.getElementsByClassName("locationID")[0].value = markers[markerIndex].id;
    //Show start time
    div.getElementsByClassName("arrival_div")[0].style.display = "block";
    
    div.getElementsByClassName("arrival_time")[0].value = start_time;
    div.getElementsByClassName("leave_time")[0].value = stop_time;
    
    div.style.display = "block";
}

//Prompt the user with a form to verify it has left
function form_leave(markerIndex){
    //TODO display form
}

function pad(n){
    return (n < 10) ? ("0" + n) : n;
}

function hide_popups(){
    var popups = document.getElementsByClassName("popup_form");
    for(var i = 0; i < popups.length; i++){
        popups[i].style.display = "none";
    }
}