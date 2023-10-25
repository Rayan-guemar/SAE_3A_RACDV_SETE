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
