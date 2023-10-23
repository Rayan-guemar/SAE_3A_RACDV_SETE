debut = document.getElementById("demande_festival_dateDebutFestival");
fin = document.getElementById("demande_festival_dateFinFestival");
debut.addEventListener("change", function() {
        debut.setAttribute("min", debut.value);
        debut.setAttribute("value", debut.value);
        fin.setAttribute("min", debut.value);
        fin.setAttribute("value", debut.value);
});