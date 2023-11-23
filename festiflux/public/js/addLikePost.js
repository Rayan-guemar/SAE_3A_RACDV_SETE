const checkboxs = document.getElementsByClassName('switch');
var radioButtons = document.querySelectorAll('input[type="radio"]');

[...radioButtons].forEach(radioButton => {

    radioButton.addEventListener('change', async function () {
        var idPoste = radioButton.getAttribute('name').replace('choice_radio', '');
        var value = radioButton.value;

        var URL = ""
        if (value==="0"){
            URL = Routing.generate('app_user_likedPoste_add',{ idPoste: idPoste })
        }else if (value==="-1"){
            URL = Routing.generate('app_user_likedPoste_remove',{ idPoste: idPoste })
        }else{
            URL = Routing.generate('app_user_AdorePoste_add',{ idPoste: idPoste })
        }
        try {
            await fetch(URL);
        } catch (error) {
            console.log(error);
        }
    });
});
