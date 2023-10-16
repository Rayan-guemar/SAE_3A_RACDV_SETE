
let button = document.getElementsByClassName("button-benevole");
    button[0].addEventListener("click",function (e) {
        let xhr = new XMLHttpRequest();

        let festId = (document.getElementsByClassName("btn btn-primary"))[0].dataset.festivalid;
        let URL = Routing.generate('app_demandebenevole_add', {'id': festId});
        xhr.open("GET",URL);
        //On veut une réponse au format JSON
        xhr.responseType = "json";
        xhr.send();
    });
