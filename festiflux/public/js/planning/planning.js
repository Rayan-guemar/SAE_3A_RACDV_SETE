import { Tache } from './Tache.js';
import { getTaches } from './backend.js';
import { dateDiff, encodedStr, getDateHours2Digits } from './utils.js';

export class Planning {
	constructor(el, dateDebut, dateFin, postes, creneaux) {
		this.html = el;
		this.dateDebut = dateDebut;
		this.dateFin = dateFin;
		this.postes = postes || [];
		this.creneaux = creneaux || [];

		this.addCreneauxBtn = document.getElementById('add-creneaux-btn');
		this.addCreneauxForm = document.getElementById('add-creneaux');

		this.postesBtn = document.getElementById('add-poste-btn');
		this.postes = document.getElementById('postes');

		this.addPostebtn = document.getElementById('add-poste-btn');
		this.addPosteForm = document.getElementById('add-poste');

		this.addListener();
		this.handlePlanning();
	}

	addListener() {
		this.addCreneauxBtn.addEventListener('click', () => {
			this.addCreneauxForm.classList.add('visible');
			this.html.classList.add('blurred');
		});

		this.addCreneauxForm.querySelector('.close-btn').addEventListener('click', () => {
			this.addCreneauxForm.classList.remove('visible');
			this.html.classList.remove('blurred');
		});

		this.postesBtn.addEventListener('click', () => {
			this.postes.classList.add('visible');
			this.html.classList.add('blurred');
		});

		this.postes.querySelector('.close-btn').addEventListener('click', () => {
			this.postes.classList.remove('visible');
			this.html.classList.remove('blurred');
		});

		this.addPostebtn.addEventListener('click', () => {
			this.addPosteForm.classList.add('visible');
		});
	}

	addPoste(poste) {
		this.postes.appendChild(poste.html);
	}

	/**
	 * Récupère la correspondance entre les dates et les divs de chaque jour.
	 * @returns {Map<string, HTMLElement>} Un objet contenant la correspondance entre les dates et les divs de chaque jour.
	 */
	getDateToDayMapping = () => {
		/**
		 * Mappe chaque date avec son div de jour correspondant.
		 * @type {Map<string, HTMLElement>}
		 */
		const dateToDayMap = new Map();

		const dayDivs = document.querySelectorAll('.day');
		const dayDivsArray = [...dayDivs];

		for (const dayDiv of dayDivsArray) {
			const date = new Date(dayDiv.getAttribute('data-date'));
			dateToDayMap.set(date.toDateString(), dayDiv);
		}

		return dateToDayMap;
	};

	/**
	 * Affiche toutes les tâches dans le planning.
	 * @param {Map<string, HTMLElement>} dateToDayMap - La correspondance entre les dates et les divs de chaque jour.
	 * @param {Tache[]} taches - Les tâches à afficher.
	 * @returns {void}
	 * @example
	 */
	afficherTouteLesTaches = (dateToDayMap, taches) => {
		for (const t of taches) {
			const date = new Date(t.creneau.debut);
			const dayDiv = dateToDayMap.get(date.toDateString());
			if (dayDiv === undefined) {
				console.error(`Aucun div de jour trouvé pour la date ${date}`);
				continue;
			}

			const taskDiv = document.createElement('div');
			taskDiv.classList.add('task');
			getDateHours2Digits;
			taskDiv.innerHTML = `
            <div class="name">${encodedStr(t.poste.nom)}</div>
            <div class="creneau">${encodedStr(`${getDateHours2Digits(t.creneau.debut)}h - ${getDateHours2Digits(t.creneau.fin)}h`)}</div>
        `;

			taskDiv.style.top = `${(t.creneau.debut.getHours() / 24) * 100}%`;
			taskDiv.style.height = `${((t.creneau.fin.getHours() - t.creneau.debut.getHours()) / 24) * 100}%`;
			dayDiv.appendChild(taskDiv);
		}
	};

	/**
	 * Gère le planning.
	 * @returns {void}
	 */
	handlePlanning = () => {
		const taches = getTaches();
		/**
		 * Récupère la correspondance entre les dates et les divs de chaque jour.
		 * @returns {Object} Un objet contenant la correspondance entre les dates et les divs de chaque jour.
		 */
		const dateToDayDivArray = this.getDateToDayMapping();
		console.log(dateToDayDivArray);
		this.afficherTouteLesTaches(dateToDayDivArray, taches);
	};
}

class Planning {
    constructor(dateDebut, dateFin, postes, creneaux) {
        this.html = document.getElementById('planning');
        this.dateDebut = dateDebut;
        this.dateFin = dateFin;
        this.postes = postes || [];
        this.creneaux = creneaux || [];

        this.addCreneauxBtn = document.getElementById('add-creneaux-btn');
        this.addCreneauxForm = document.getElementById('add-creneaux');

        this.postesBtn = document.getElementById('poste-btn');
        this.postes = document.getElementById('postes');

        this.addPostebtn = document.getElementById('add-poste-btn');
        this.addPosteForm = document.getElementById('add-poste');

        this.numberOfDays = dateDiff(this.dateDebut, this.dateFin).day + 1;

        this.days = this.html.querySelector('.days');

        this.dayNames = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

        this.addListener();
    }

    addListener() {
        // Lorsque l'on clique sur le bouton "Ajouter des créneaux", on affiche le formulaire de création et on floute le reste de la page
        this.addCreneauxBtn.addEventListener('click', () => {
            this.addCreneauxForm.classList.add('visible');
            this.html.classList.add('blurred');
        });

        // Lorsque l'on clique sur le bouton "Fermer" du formulaire de création de créneaux, on cache le formulaire et on enlève le flou de la page
        this.addCreneauxForm.querySelector('.close-btn').addEventListener('click', () => {
            this.addCreneauxForm.classList.remove('visible');
            this.html.classList.remove('blurred');
        });

        // Lorsque l'on clique sur le bouton "Poste", on affiche les postes et on floute le reste de la page
        this.postesBtn.addEventListener('click', () => {
            this.postes.classList.add('visible');
            this.html.classList.add('blurred');
        })

        // Lorsque l'on clique sur le bouton "Fermer" des postes, on cache les postes et on enlève le flou de la page
        this.postes.querySelector('.close-btn').addEventListener('click', () => {
            this.postes.classList.remove('visible');
            this.html.classList.remove('blurred');
        })

        // Lorsque l'on clique sur le bouton "Ajouter un poste", on affiche le formulaire de création de poste et on cache les postes
        this.addPostebtn.addEventListener('click', () => {
            this.postes.classList.add('opacity-0');
            this.addPosteForm.classList.add('visible');
        })

        // Lorsque l'on clique sur le bouton "Fermer" du formulaire de création de poste, on cache le formulaire et on affiche les postes
        this.addPosteForm.querySelector('.close-btn').addEventListener('click', () => {
            this.addPosteForm.classList.remove('visible');
            this.postes.classList.remove('opacity-0');
        })

        // Lorsque l'on clique sur la flèche de gauche, les jours défilent vers la gauche
        document.getElementById('scroll-btn-left').addEventListener('click', () => {
            this.scrollDaysLeft();
        })

        // Lorsque l'on clique sur la flèche de droite, les jours défilent vers la droite
        document.getElementById('scroll-btn-right').addEventListener('click', () => {
            this.scrollDaysRight();
        })
    }

    initDays() {

    }

    addPoste(poste) {
        this.postes.appendChild(poste.html);
    }

    scrollDaysLeft() {
        let daysWidth = this.days.getBoundingClientRect().width;
        let dayWidth = this.days.querySelector('.day').getBoundingClientRect().width;
        let scroll = this.days.scrollLeft - Math.floor(daysWidth / dayWidth) * dayWidth;
        if (scroll < 0) {
            scroll = 0;
        }
        this.days.scrollTo({
            left: scroll,
            behavior: 'smooth'
        })
    }

    dayHTML(date) {
        let lineBreaker = "";
        for (let i = 1; i < 11; i++) {
            lineBreaker += `
                <div class="line-break" id="line-break-${ i * 2 }"></div>
            `
        }
        
        let html = `
            <div class="day">
                <div class="heading">11 / 09</div>
                    
                <div class="task">
                    <div class="name">Accueil artiste</div>
                    <div class="creneau">15h - 17h</div>
                </div>
            </div>
        `
    }

    scrollDaysRight() {
        let daysWidth = this.days.getBoundingClientRect().width;
        let dayWidth = this.days.querySelector('.day').getBoundingClientRect().width;
        let scroll = this.days.scrollLeft + Math.floor(daysWidth / dayWidth) * dayWidth;
        if (scroll > this.days.scrollWidth) {
            scroll = this.days.scrollWidth;
        }
        this.days.scrollTo({
            left: scroll,
            behavior: 'smooth'
        })
    }

}

let planning = new Planning(Date.parse('2019-07-01'), Date.parse('2019-07-07'));
