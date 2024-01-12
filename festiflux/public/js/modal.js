const modal = document.querySelector(".modal");
const overlay = document.querySelector(".overlay");
const openModalBtns = document.querySelectorAll(".btn-open");
const closeModalBtn = document.querySelector(".btn-close");

const openModal = function () {
    modal.classList.remove("hidden");
    overlay.classList.remove("hidden");
};

const closeModal = function () {
    modal.classList.add("hidden");
    overlay.classList.add("hidden");
};


openModalBtns.forEach(btn => btn.addEventListener("click", openModal));
closeModalBtn.addEventListener("click", closeModal);
overlay.addEventListener("click", closeModal);

document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && !modal.classList.contains("hidden")) {
        modalClose();
    }
});


