const checkboxs = document.getElementsByClassName('switch');

[...checkboxs].forEach(checkbox => {
    const idPoste = checkbox.id;
    checkbox.addEventListener('change', async function () {
        const isChecked = checkbox.checked;
        const URL = Routing.generate(isChecked ? 'app_user_likedPoste_add' : 'app_user_likedPoste_remove', { idPoste: idPoste });
        try {
            await fetch(URL);
        } catch (error) {
            console.log(error);
            checkbox.checked = !isChecked;
        }
    });
});
