// Initialisation de la carte
var mymap = L.map('mapid').setView([46.485935, 2.553603], 6);

// Ajout de la carte OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
}).addTo(mymap);


function init(){
    mymap.setView([46.485935, 2.553603], 6);
}
