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
        try {
            data = await response.json();
            data.forEach(festival => {
                let lat = festival.latitude;
                let lon = festival.longitude;
                marker = L.marker([lat, lon]).addTo(map);
                //Sets the view of the map (geographical center and zoom) with the given animation options.
            });
        } catch (error) {
            data = {};
        }
    }
}
getAllFestival();
