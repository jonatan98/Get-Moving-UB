

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
    L.marker([59.96251369439251, 10.73164701461792], {icon: gmIcon}).addTo(map).bindPopup("Nordberg Skole Basket"),
    L.marker([59.9438244699759, 10.752203464508058], {icon: gmIcon}).addTo(map).bindPopup("Voldsløkka sandvolleyball"),
    L.marker([59.94372774638071, 10.750722885131838], {icon: gmIcon}).addTo(map).bindPopup("Voldsløkka kunstgress"),
    L.marker([59.923224037664475, 10.75514316558838], {icon: gmIcon}).addTo(map).bindPopup("Kubaparken (Grünerløkka)"),
    L.marker([59.87636118779293, 10.690383911132812], {icon: gmIcon}).addTo(map).bindPopup("Prima badeplass")
];

/* Find coordinates on click

map.on('click', function(e) {
    console.log("[" + e.latlng.lat + ", " + e.latlng.lng + "]")
});
*/