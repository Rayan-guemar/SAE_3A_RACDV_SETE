const overlay = document.querySelector(".overlay");
const openModalBtns = document.querySelectorAll(".btn-open");
const closeModalBtns = document.querySelectorAll(".btn-close");
const modals = document.querySelectorAll(".modal");

const openModal = function (modalId) {
    const modal = document.getElementById(`modal-${modalId}`);
    modal.classList.remove("hidden");
    overlay.classList.remove("hidden");
};

const closeModal = function (modalId) {
    const modal = document.getElementById(`modal-${modalId}`);
    modal.classList.add("hidden");
    overlay.classList.add("hidden");
};

openModalBtns.forEach(btn => {
    btn.addEventListener("click", function () {
        const modalId = this.getAttribute('data-validation-id');
        openModal(modalId);
    });
});

closeModalBtns.forEach(btn => {
    btn.addEventListener("click", function () {
        const modalId = this.closest('.modal').getAttribute('id').replace('modal-', '');
        closeModal(modalId);
    });
});

overlay.addEventListener("click", function () {
    modals.forEach(modal => {
        const modalId = modal.getAttribute('id').replace('modal-', '');
        closeModal(modalId);
    });
});

document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        modals.forEach(modal => {
            const modalId = modal.getAttribute('id').replace('modal-', '');
            closeModal(modalId);
        });
    }
});
