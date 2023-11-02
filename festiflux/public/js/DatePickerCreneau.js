function changeHandlerDebut() {
    debut = document.getElementById("start-creneau");
    fin = document.getElementById("end-creneau");
    debut.setAttribute("value", debut.value);
    fin.setAttribute("min", debut.value);
}

function changeHandlerFin() {
    debut = document.getElementById("start-creneau");
    fin = document.getElementById("end-creneau");
    fin.setAttribute("value", fin.value);
    debut.setAttribute("max", fin.value);
}