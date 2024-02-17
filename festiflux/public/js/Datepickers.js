debut = document.getElementById("festival_dateDebut");
fin = document.getElementById("festival_dateFin");
debut.addEventListener("change", function() {
        debut.setAttribute("value", debut.value);
        fin.setAttribute("min", debut.value);
        fin.setAttribute("value", debut.value);
});

fin.addEventListener("change", function() {
        debut.setAttribute("max", fin.value);
});