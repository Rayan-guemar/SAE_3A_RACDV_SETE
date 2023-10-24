const festId = document.getElementById("id_festival").value;


// Sélectionnez la case à cocher par son ID
const checkboxs = document.getElementsByClassName("switch");

// Ajoutez un écouteur d'événements "change" pour détecter les changements d'état de la case à cocher
[...checkboxs].forEach(checkbox => {
    let userId = (checkbox.id)
    let xhr = new XMLHttpRequest();
    let URL = Routing.generate('app_festival_verif_responsable', {'festId': festId, 'userId': userId});
    xhr.open("GET", URL);
    //On veut une réponse au format JSON
    xhr.responseType = "json";
    xhr.onload = function () {
        //Fonction déclenchée quand on reçoit la réponse du serveur.
        //xhr.status permet d'accèder au code de réponse HTTP (200, 204, 403, 404, etc...)
        if (xhr.status===200){
            const data = JSON.parse(xhr.response);
            if (data===true){
                checkbox.checked=true;
            }
        }else {
            console.log(Error(xhr.status));
        }
    }
    xhr.send();

    checkbox.addEventListener("change", function () {
        // Récupérez la valeur de la case à cocher (true si cochée, false sinon)
        const isChecked = checkbox.checked;


        // Utilisez la valeur isChecked comme bon vous semble
        if (isChecked) {
            console.log("La case à cocher est cochée.");
            let xhr = new XMLHttpRequest();

            let URL = Routing.generate('app_festival_add_responsable', {'festId': festId, 'userId': userId});
            xhr.open("GET", URL);
            //On veut une réponse au format JSON
            xhr.responseType = "json";
            xhr.send();
        } else {
            console.log("La case à cocher n'est pas cochée.");
            let xhr = new XMLHttpRequest();

            let URL = Routing.generate('app_festival_remove_responsable', {'festId': festId, 'userId': userId});
            xhr.open("GET", URL);
            //On veut une réponse au format JSON
            xhr.responseType = "json";
            xhr.send();

        }
    });
})


