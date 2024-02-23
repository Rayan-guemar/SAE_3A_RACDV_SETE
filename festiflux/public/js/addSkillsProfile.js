const addButton = document.querySelector('.btn-add-skills')
const skill =
    `<div class="skills-div">
        <input class="skills-inputs nom" placeholder="Nom">
        <input class="skills-inputs degre" placeholder="Niveau de maîtrise">
     </div>`

const skillsContainer = document.querySelector('.skills')
const skill_div = document.querySelectorAll('.skills-div')

addButton.addEventListener('click', () => {
    skillsContainer.innerHTML += skill;
})


for (let div of skill_div)  {
    div.addEventListener('click', () => {
        div.remove()
    })
}