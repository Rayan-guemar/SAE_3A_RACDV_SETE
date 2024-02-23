const addButton = document.querySelector('.btn-add-skills')
const skillsContainer = document.querySelector('.skills')

addButton.addEventListener('click', () => {
    const skill = document.createElement('div')
    skill.classList.add('skills-div')
    skill.innerHTML = ` <input class="skills-inputs nom" placeholder="Nom">
                    <input class="skills-inputs degre" placeholder="Niveau de maîtrise">
                    <div class="croix">X</div>`

    skillsContainer.appendChild(skill);
    const croix = skill.querySelector('.croix');
    croix.addEventListener('click', () => {
        croix.parentElement.remove();
    });
})
