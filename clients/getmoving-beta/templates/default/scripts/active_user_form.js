
//Hide form cover
function hide_form_cover(){
    document.getElementById("form_cover").style.display = "none";
    hide_popups();
}

//Promt the user with a form questioning for how long it will be there
function form_ishere(markerIndex){
    hide_popups();
    document.getElementById("form_cover").style.display = "block";
    var div = document.getElementById("form_ishere");
    
    div.getElementsByClassName("locationID")[0].value = markers[markerIndex].id;
    
    var now = new Date();
    var sooner = new Date(now.setHours((now).getHours()+2));
    var lt = div.getElementsByClassName("leave_time")[0].value = pad(sooner.getHours()) + ":" + pad(sooner.getMinutes());
    
    div.style.display = "block";
}

//Prompt the user with a form questioning when and for how long it will be there
function form_willbehere(markerIndex){
    hide_popups();
    document.getElementById("form_cover").style.display = "block";
    var div = document.getElementById("form_willbehere");
    
    div.getElementsByClassName("locationID")[0].value = markers[markerIndex].id;
    
    var now = new Date();
    var at = div.getElementsByClassName("arrival_time")[0].value = pad(now.getHours()) + ":" + pad(now.getMinutes());
    var sooner = new Date(now.setHours((now).getHours()+2));
    var lt = div.getElementsByClassName("leave_time")[0].value = pad(sooner.getHours()) + ":" + pad(sooner.getMinutes());
    
    div.style.display = "block";
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