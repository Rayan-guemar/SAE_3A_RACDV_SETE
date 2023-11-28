async function getAllFestival() {

    // Initialisation de la carte
    var map = L.map('map').setView([46.485935, 2.553603], 6);

    // Ajout de la carte OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
    }).addTo(map);

    const response = await fetch(Routing.generate('app_all_festival'));
    if (!response.ok) {
        throw new Error(await response.text());
    }
    else {
        let data;
        let layer = new L.layerGroup();
        try {
            data = await response.json();
            data.forEach(festival => {
                new L.Marker([festival.latitude, festival.longitude])
                console.log(festival.longitude);
                console.log(festival.latitude);
            });
            layer.addTo(map);
        } catch (error) {
            data = {};
        }
    }
}
getAllFestival();
