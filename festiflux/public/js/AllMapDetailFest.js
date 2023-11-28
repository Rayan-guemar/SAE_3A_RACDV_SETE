async function getAllFestival() {
    const response = await fetch(Routing.generate('app_all_festival'));
    if (!response.ok) {
        throw new Error(await response.text());
    }
    else {
        let data;
        try {
            data = await response.json();
        } catch (error) {
            data = {};
        }
        return data;
    }
}
/*
// Initialisation de la carte
var mymap = L.map('mapid').setView([46.485935, 2.553603], 6);

// Ajout de la carte OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
}).addTo(mymap);
*/
console.log((async () => {
    let a =await getAllFestival()
    console.log(a)
})());
/*
let lat = document.getElementById("lat").value;
let lon = document.getElementById("lon").value;

//Set a marker on the map
marker = L.marker([lat, lon]).addTo(mymap);
//Sets the view of the map (geographical center and zoom) with the given animation options.
mymap.setView([lat, lon], 10);
*/