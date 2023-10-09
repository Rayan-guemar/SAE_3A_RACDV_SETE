// Initialisation de la carte
var mymap = L.map('mapid').setView([46.485935, 2.553603], 6);


function init() {
// Récupérez l'élément HTML qui contient les données du festival par son ID
    var festInfoElement = document.getElementById('fest-info');

// Récupérez les données JSON de l'attribut data-festival
    var festivalData = festInfoElement.getAttribute('data-festival');

// Analysez les données JSON en un objet JavaScript
    var festivalObject = JSON.parse(festivalData);

    console.log(festivalObject);
    //add marker
    marker = L.marker([festivalObject.lat, festivalObject.lon]).addTo(map);
    //Sets the view of the map (geographical center and zoom) with the given animation options.
    mymap.setView([festivalObject.lat, festivalObject.lon], 20);

    // mymap.setView([46.485935, 2.553603], 6);
}

// Ajout de la carte OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
}).addTo(mymap);



let containerElement = document.getElementById("box-adresses");
let adresse = document.getElementById('demande_festival_lieuFestival');
var currentItems;

function addressAutocomplete(containerElement) {
    /* Active request promise reject function. To be able to cancel the promise when a new request comes */
    var currentPromiseReject;

    adresse.addEventListener("input", function (e) {
        var currentValue = adresse.value;
        // console.log(currentValue);
        // Cancel previous request promise
        if (currentPromiseReject) {
            currentPromiseReject({
                canceled: true
            });
        }

        if (!currentValue) {
            return false;
        }

        /* Create a new promise and send geocoding request */
        var promise = new Promise((resolve, reject) => {
            currentPromiseReject = reject;

            var apikey = "360dc96e9d1b4d79a30d2c222960aba1";
            var url = `https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(currentValue)}&limit=5&apiKey=${apikey}`;

            fetch(url)
                .then(response => {
                    // check if the call was successful
                    if (response.ok) {
                        response.json().then(data => resolve(data));
                    } else {
                        response.json().then(data => reject(data));
                    }
                });
        });

        /* Close any already open dropdown list */
        closeDropDownList();

        promise.then((data) => {
            currentItems = data.features;
            /*create a DIV element that will contain the items (values):*/
            var autocompleteItemsElement = document.createElement("div");
            autocompleteItemsElement.setAttribute("class", "autocomplete-items");
            containerElement.appendChild(autocompleteItemsElement);

            /* For each item in the results */
            data.features.forEach((feature, index) => {
                /* Create a DIV element for each element: */
                var itemElement = document.createElement("DIV");
                /* Set formatted address as item value */
                itemElement.innerHTML = feature.properties.formatted;
                /* Set the value for the autocomplete text field and notify: */
                itemElement.addEventListener("click", function(e) {
                    adresse.value = currentItems[index].properties.formatted;
                    /* Close the list of autocompleted values: */
                    closeDropDownList();
                });

                autocompleteItemsElement.appendChild(itemElement);
            });

        }, (err) => {
            if (!err.canceled) {
                console.log(err);
            }
        });

        function closeDropDownList() {
            var autocompleteItemsElement = containerElement.querySelector(".autocomplete-items");
            if (autocompleteItemsElement) {
                containerElement.removeChild(autocompleteItemsElement);
            }
        }
    });


}



init();
