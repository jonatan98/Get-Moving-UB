

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
    L.marker(pos, {icon: gmIcon}).addTo(map).bindPopup("Sted 1"),
    L.marker([59.943445634283, 10.789679288864138], {icon: gmIcon}).addTo(map).bindPopup("fam Vedals place"),
    L.marker([59.944243602294385, 10.793724060058594], {icon: gmIcon}).addTo(map).bindPopup("Julez crib"),
    L.marker([59.965346509857575, 10.758377909660341], {icon: gmIcon}).addTo(map).bindPopup("Juanatans place"),
    L.marker([59.96549687054325, 10.922014117240906], {icon: gmIcon}).addTo(map).bindPopup("Kellz place")
];