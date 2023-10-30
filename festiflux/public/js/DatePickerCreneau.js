function changeHandlerDebut() {
    debut = document.getElementById("start-creneau");
    fin = document.getElementById("end-creneau");
    console.log("debut");
    console.log(debut.value);
    console.log("fin");
    console.log(fin.value);
    debut.setAttribute("value", debut.value);
    fin.setAttribute("min", debut.value);
    console.log("fin min");
    console.log(fin.min);
}

function changeHandlerFin() {
    debut = document.getElementById("start-creneau");
    fin = document.getElementById("end-creneau");
    console.log("debut");
    console.log(debut.value);
    console.log("fin");
    console.log(fin.value);
    fin.setAttribute("value", fin.value);
    debut.setAttribute("max", fin.value);
    console.log("debut max");
    console.log(debut.max);
}