
/*
 * Set the default styling of #toggle_menu
 */

$(function(){
    var lines = this.getElementsByClassName("line");
    if($('body').width() <= 800){
        //Mobile
        document.getElementById("toggle_menu").setAttribute("data-active", "1");
        $("#toggle_menu").css("marginTop", ($(lines[0]).width()/2)*Math.cos(45) + "px");
        var rotate = [45, -45, -45];
        for(var i = 0; i < 3; i++){
            lines[i].style.position = "absolute";
            lines[i].style.transform = "rotate(" + rotate[i] + "deg)";
        }
    }else{
        //Desktop
        document.getElementById("toggle_menu").setAttribute("data-active", "1");
        $("#toggle_menu").css("marginTop", ($(lines[0]).width()/2)*Math.cos(45) + "px");
        var rotate = [45, -45, -45];
        for(var i = 0; i < 3; i++){
            lines[i].style.position = "absolute";
            lines[i].style.transform = "rotate(" + rotate[i] + "deg)";
        }
    }
});
 
/*
 * Define what happens when #toggle_menu has been clicked
 */
document.getElementById("toggle_menu").addEventListener('click', function(){
    var lines = this.getElementsByClassName("line");
    if(this.getAttribute("data-active") == 1){
        //Close menu
        this.setAttribute("data-active", "0");
        $("#toggle_menu").css("marginTop", "0px");
        for(var i = 0; i < 3; i++){
            lines[i].style.transform = "";
            lines[i].style.marginTop = "initial";
            lines[i].style.position = "relative";
        }
        if($('body').width() <= 800){
            //Mobile layout
            document.getElementById("sidebar").style.display = "none";
        }else{
            //Desktop layout
            $('#sidebar').animate({width: "0px"}, 500, function(){
                $('#sidebar').hide();
            });
            $('#toggle_menu').animate({left: "0px"}, 400);
        }
    }else{
        //Open menu
        this.setAttribute("data-active", "1");
        //Calculate the height of half a crossed bar
        document.getElementById("toggle_menu").style.height = $(lines[2]).height() + $(lines[2]).offset().top - 10 + "px";
        $("#toggle_menu").css(
            {paddingTop: ($(lines[0]).width()/2)*Math.cos(45) + "px"}
        );
        $("#toggle_menu .line").css(
            {marginTop: ($(lines[0]).width()/2)*Math.cos(45) + "px"}
        );
        var rotate = [45, -45, -45];
        for(var i = 0; i < 3; i++){
            lines[i].style.position = "absolute";
            lines[i].style.transform = "rotate(" + rotate[i] + "deg)";
        }
        if($('body').width() <= 800){
            //Mobile layout
            $('#sidebar > ul li:first-of-type').css({marginTop: "-3px"});
            $('#sidebar > ul li').css({ opacity: "0" });
            document.getElementById("sidebar").style.display = "block";
            $('#sidebar > ul li:first-of-type').animate({ marginTop: "0px", opacity: "1"}, 500);
            $('#sidebar > ul li').animate({ opacity: "1" }, 500);
        }else{
            //Desktop layout
            $('#sidebar').show();
            $('#sidebar').animate({width: "250px"}, 500);
            setTimeout(function(){
                $('#toggle_menu').animate({left: "200px"}, 400);
            }, 100);
        }
    }
});

//Get the user's position
var x = document.getElementById("demo");
var pos = [59.929904118285846, 10.754928588867188];
var zoom = 13;

var map = L.map('map', {zoomControl: false}).setView(pos, zoom);
L.tileLayer('https://opencache.statkart.no/gatekeeper/gk/gk.open_gmaps?layers=norges_grunnkart&zoom={z}&x={x}&y={y}').addTo(map);

//add zoom control with your options
L.control.zoom({
     position:'topleft'
}).addTo(map);

