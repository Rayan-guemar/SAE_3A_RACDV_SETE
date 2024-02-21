let divs = document.getElementsByClassName('df')
let btn = document.getElementsByClassName('edit-button')

for (let div of divs) {
    const pen = div.querySelector('.pen')
    const input = div.querySelector('input')
    pen.addEventListener('click', () => {
        if (input.disabled) {
            input.classList.add('edit-hover')
            pen.classList.add('pen-hover')
            pen.classList.remove('pen')
            input.disabled = false
            btn[0].style.display = "flex"
        }
        else {
            input.classList.remove('edit-hover')
            pen.classList.remove('pen-hover')
            pen.classList.add('pen')
            input.disabled = true;
        }
    })
}