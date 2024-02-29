let divs = document.getElementsByClassName('df')
let btn = document.getElementsByClassName('edit-button')

for (let div of divs) {
    const pen = div.querySelector('.pen')
    const input = div.querySelector('input')
    pen.addEventListener('click', () => {
        const image = pen.querySelector('.pen-img')
        if (image.attributes.src.value === "/icons/pen-black.svg") {
            image.attributes.src.value = "/icons/pen-green.svg"
        }
        else {
            image.attributes.src.value = "/icons/pen-black.svg"
        }

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

btn[0].addEventListener('click', () => {
    for (let div of divs) {
        const input = div.querySelector('input')
        input.disabled = false
    }
})