import { dateDiff } from './utils.js';
import { TacheElement } from './Element/TacheElement.js';
import { Tache } from './Entity/Tache.js';
import { TachesManager } from './TachesManager.js';
import { modalManager } from './ModalManager.js';
import { testForm } from './Form.js';

export class Planning {
	constructor(festId, dateDebut, dateFin, taches = []) {
		this.festId = festId;
		this.html = document.getElementById('planning');
		this.dateDebut = new Date(dateDebut);
		this.dateFin = new Date(dateFin);
		this.tacheElements = [];

		this.tachesManager = new TachesManager(festId, taches);
		this.tachesManager.onSortedTachesUpdate(this.showTaches);

		if (this.dateDebut > this.dateFin) {
			throw new Error('La date de début doit être antérieure à la date de fin.');
		}
		if (this.dateDebut.toString() === 'Invalid Date') {
			throw new Error("La date de début n'est pas valide.");
		}

		this.numberOfDays = dateDiff(this.dateDebut, this.dateFin).day + 1;
		this.days = this.html.querySelector('.days');
		this.dateToDayMap = this.getDateToDayMapping();

		this.dayNames = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
		this.monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

		this.init();

		this.html.classList.remove('loading');
		document.getElementById('loader').remove();

		modalManager.openWith('form test', testForm.getForm());
	}

	init() {
		this.initDays();
		this.addListeners();
		this.tachesManager.update();
	}

	/**
	 * Initialise les jours du planning en générant le code HTML correspondant.
	 * @function
	 * @returns {void}
	 */
	initDays() {
		let html = '';
		for (let i = 0; i < this.numberOfDays; i++) {
			let date = new Date(this.dateDebut);
			date.setDate(date.getDate() + i);
			html += this.dayHTML(date);
		}
		this.days.innerHTML = html;
	}

	/**
	 * Génère le HTML pour un jour donné.
	 * @param {Date} date - La date du jour.
	 * @returns {string} Le code HTML du jour.
	 */
	dayHTML(date) {
		let lineBreaker = '';
		for (let i = 1; i <= 11; i++) {
			lineBreaker += `
                <div class="line-break" id="line-break-${i * 2}"></div>
            `;
		}
		let html = `
            <div class="day" data-date=${date.toISOString()}>
                <div class="heading">${this.dayNames[date.getDay()].substring(0, 3)} ${date.getDate()} ${this.monthNames[date.getMonth()].substring(0, 4)}.</div>
                ${lineBreaker}
            </div>
        `;

		return html;
	}

	showTaches() {
		const sortedTaches = this.tachesManager.sortedTaches;
		this.clearTaches();
		console.log(sortedTaches);
		for (const taches of sortedTaches) {
			for (let i = 0; i < taches.length; i++) {
				t = taches[i];
				const tElement = new TacheElement(t, i, taches.length);
				this.tacheElements.push(tElement.htmlNode());
			}
		}

		for (const tElement of this.tacheElements) {
			const date = new Date(tElement.dataset.date);
			const day = this.dateToDayMap.get(date.toDateString());
			if (!day) console.error(`Le jour ${date.toDateString()} n'existe pas.`);
			else day.appendChild(tElement);
		}
	}

	clearTaches() {
		this.tacheElements.forEach(tache => tache.element?.remove());
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
	 * Ajoute les écouteurs d'événements pour les boutons et formulaires de la page de planification.
	 * @returns {void}
	 */
	addListeners() {
		// Lorsque l'on clique sur la flèche de gauche, les jours défilent vers la gauche
		document.getElementById('scroll-btn-left').addEventListener('click', () => {
			this.scrollDaysLeft();
		});

		// Lorsque l'on clique sur la flèche de droite, les jours défilent vers la droite
		document.getElementById('scroll-btn-right').addEventListener('click', () => {
			this.scrollDaysRight();
		});
	}

	/**
	 * Fait défiler les jours vers la gauche.
	 * @function
	 * @name scrollDaysLeft
	 * @memberof Planning
	 * @instance
	 * @returns {void}
	 */
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
		});
	}

	/**
	 * Fait défiler les jours vers la droite.
	 * @function
	 * @name scrollDaysRight
	 * @memberof Planning
	 * @instance
	 * @returns {void}
	 */
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
		});
	}
}
