let divs = document.getElementsByClassName('info-border')
let btn = document.getElementsByClassName('edit-button')
for (let div of divs) {
    const input = div.querySelector('.input-profile')
    const crayon = div.querySelector('a')
    crayon.addEventListener("click", ()=> {
        input.disabled = false;
        console.log('test')
    })
}

