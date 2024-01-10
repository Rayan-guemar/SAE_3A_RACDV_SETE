const modal = document.querySelector(".modal");
const overlay = document.querySelector(".overlay");
const submitBtn = document.querySelector(".btn");
const openModalBtn = document.querySelector(".btn-open");
const closeModalBtn = document.querySelector(".btn-close");

var motif = document.getElementById("motif").value;
var idUtilisateur = document.getElementById("id_utilisateur").value;
var idFestival = document.getElementById("id_festival").value;

console.log(idUtilisateur);
console.log(idFestival);

const openModal = function () {
    modal.classList.remove("hidden");
    overlay.classList.remove("hidden");
};

openModalBtn.addEventListener("click", openModal);

const closeModal = function () {
    modal.classList.add("hidden");
    overlay.classList.add("hidden");
};

closeModalBtn.addEventListener("click", closeModal);
overlay.addEventListener("click", closeModal);


document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && !modal.classList.contains("hidden")) {
        modalClose();
    }
});

submitBtn.addEventListener("click", async function (e) {
    const URL = Routing.generate('app_festival_rejectAndSendMotif', {
        idUser: idUtilisateur,
        id: idFestival
    });
    try {
        await fetch(URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                "motif": motif
            }),
        });
    } catch (error) {
        console.log(error);
    }
    closeModal();
    window.location.reload();

});