const btn = document.querySelector(".map-btn");
const map = document.getElementById("map");

btn.addEventListener('click', () => {
    if (btn.innerText === "Afficher la carte") {
        map.style.display = "block";
        btn.innerText = "Cacher la carte"
    }
    else if (btn.innerText === "Cacher la carte") {
        map.style.display = "none";
        btn.innerText = "Afficher la carte"
    }
})


async function getAllFestival() {

    // Initialisation de la carte
    var map = L.map('map').setView([46.485935, 2.553603], 6);

    // Ajout de la carte OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 12,
        attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
    }).addTo(map);

    const response = await fetch(Routing.generate('app_all_festival'));
    if (!response.ok) {
        throw new Error(await response.text());
    }
    else {
        let data;
        let layer = L.layerGroup().addTo(map);
        try {
            data = await response.json();
            data.forEach(festival => {
                if (festival.latitude && festival.longitude) {
                    let coordinates = new L.Marker([festival.latitude, festival.longitude])
                    layer.addLayer(coordinates)
                    function redirectToFest() {
                        window.location = Routing.generate('app_festival_detail', { id: festival.id })
                    }
                    coordinates.on('click', redirectToFest)
                }
            });
            var overlay = { 'markers': layer };
            L.control.layers(null, overlay).addTo(map);
        } catch (error) {
            data = {};
        }
    }
}
getAllFestival();
