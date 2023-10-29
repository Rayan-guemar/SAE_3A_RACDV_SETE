import { dateDiff } from './utils.js';
import { Poste } from './Poste.js';
import { Backend } from './Backend.js';
import { Creneau } from './Creneau.js';
import { Tache } from './Tache.js';

/**
 * Classe représentant un planning.
 */
export class Planning {
	/**
	 * Crée une instance de la classe Planning.
	 * @constructor
	 * @param {number} festId - L'ID du festival.
	 * @param {Date} dateDebut - La date de début du planning.
	 * @param {Date} dateFin - La date de fin du planning.
	 * @param {Poste[]} postes - Les postes associés au planning.
	 * @param {Creneau[]} creneaux - Les créneaux du planning.
	 */
	constructor(festId, dateDebut, dateFin) {
		this.festId = festId;
		this.html = document.getElementById('planning');
		this.dateDebut = new Date(dateDebut);
		this.dateFin = new Date(dateFin);
		this.dateFin.setHours(23, 59, 59, 999);
		this.postes = [];
		this.creneaux = [];

		this.addCreneauxBtn = document.getElementById('add-creneau-btn');
		this.addCreneauxForm = document.getElementById('add-creneau');
		this.creneauDescription = document.getElementById('creneau-description');
		this.creneauNombreBenevole = document.getElementById('creneau-nombre-benevole');
		this.startCreneauxInput = document.getElementById('start-creneau');
		this.endCreneauxInput = document.getElementById('end-creneau');
		this.creneauPosteSelect = document.getElementById('creneau-poste-select');
		this.createCreneauxBtn = document.getElementById('create-creneau-btn');

		this.addPostebtn = document.getElementById('add-poste-btn');
		this.addPosteForm = document.getElementById('add-poste');
		this.createPosteBtn = document.getElementById('create-poste-btn');
		this.createPosteInput = document.getElementById('poste-name');

		this.postesEl = document.querySelector('.postes');

		this.numberOfDays = dateDiff(this.dateDebut, this.dateFin).day + 1;

		this.days = this.html.querySelector('.days');

		this.dayNames = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
		this.monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

		this.init();
	}

	/**
	 * Initialise le planning en récupérant les données et en configurant les écouteurs d'événements.
	 * @async
	 * @returns {Promise<void>}
	 */
	async init() {
		this.initDays();
		this.addListener();
		await this.refreshPostesList();

		document.getElementById('loader').style.display = 'none';
		this.html.classList.remove('loading');
	}

	/**
	 * Ajoute les écouteurs d'événements pour les boutons et formulaires de la page de planification.
	 * @returns {void}
	 */
	addListener() {
		// Lorsque l'on clique sur le bouton "Ajouter des créneaux", on affiche le formulaire de création et on floute le reste de la page
		this.addCreneauxBtn.addEventListener('click', () => {
			this.creneauPosteSelect.innerHTML = '<option value="">Choisissez une option <option/>' + this.postes.map(poste => `<option value="${poste.id}">${poste.nom}</option>`).join('');
			this.addCreneauxForm.classList.add('visible');
			this.html.classList.add('blurred');
		});

		// Lorsque l'on clique sur le bouton "Fermer" du formulaire de création de créneaux, on cache le formulaire et on enlève le flou de la page
		this.addCreneauxForm.querySelector('.close-btn').addEventListener('click', () => {
			this.addCreneauxForm.classList.remove('visible');
			this.html.classList.remove('blurred');
		});

		// Lorsque l'on clique sur le bouton "Ajouter un poste", on affiche le formulaire de création de poste et on cache les postes
		this.addPostebtn.addEventListener('click', () => {
			this.addPosteForm.classList.add('visible');
			this.html.classList.add('blurred');
		});

		// Lorsque l'on clique sur le bouton "Fermer" du formulaire de création de poste, on cache le formulaire et on enlève le flou de la page
		this.addPosteForm.querySelector('.close-btn').addEventListener('click', () => {
			this.addPosteForm.classList.remove('visible');
			this.html.classList.remove('blurred');
		});

		// Lorsque l'on clique sur la flèche de gauche, les jours défilent vers la gauche
		document.getElementById('scroll-btn-left').addEventListener('click', () => {
			this.scrollDaysLeft();
		});

		// Lorsque l'on clique sur la flèche de droite, les jours défilent vers la droite
		document.getElementById('scroll-btn-right').addEventListener('click', () => {
			this.scrollDaysRight();
		});

		// Lorsque l'on clique sur le bouton "Créer" du formulaire de création de poste, on crée le poste
		this.createPosteBtn.addEventListener('click', () => {
			let poste = Poste.new(this.createPosteInput.value);
			this.addPoste(poste);
			this.addPosteForm.classList.remove('visible');
			this.html.classList.remove('blurred');
		});

		// Lorsque l'on clique sur le bouton "Créer" du formulaire de création de créneaux, on crée le créneaux
		this.createCreneauxBtn.addEventListener('click', async () => {
			const debut = new Date(this.startCreneauxInput.value);
			const fin = new Date(this.endCreneauxInput.value);
			const description = this.creneauDescription.value;
			const nbBenevole = +this.creneauNombreBenevole.value || 0;
			const posteId = +this.creneauPosteSelect.value;

			if (debut > fin) return alert('La date de début doit être inférieure à la date de fin');
			if (debut <= this.dateDebut || fin >= this.dateFin) return alert('Les créneaux doivent être compris dans la période du festival');
			if (!posteId) return alert('Veuillez choisir un poste');
			if (!nbBenevole) return alert('Veuillez choisir un nombre de bénévoles');

			const p = this.postes.find(poste => poste.id === posteId);
			if (!p) return console.error('Poste introuvable');

			const c = new Creneau(null, this.startCreneauxInput.value, this.endCreneauxInput.value);
			const t = new Tache(null, this.creneauDescription.value, nbBenevole, p, c);

			//TODO: ajouter créneau sur l'edt

			this.addCreneauxForm.classList.remove('visible');
			this.html.classList.remove('blurred');

			await Backend.addTache(this.festId, t);
		});
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
	 * Génère le code HTML pour les postes.
	 * @function
	 * @returns {void}
	 */
	renderPostes() {
		if (this.postes.length === 0) {
			this.postesEl.innerHTML = `
                <div class="no-poste">Aucun poste n'a été créé pour le moment</div>
                <div class="no-poste">Veuillez en créer un</div>
            `;
		} else {
			this.postesEl.innerHTML = this.postes.map(poste => poste.html()).join('');
		}
	}

	/**
	 * Rafraîchit la liste des postes en récupérant les données du backend.
	 * @async
	 * @returns {Promise<void>}
	 */
	async refreshPostesList() {
		this.postes = await Backend.fetchPostes(this.festId);
		this.renderPostes();
	}

	/**
	 * Ajoute un poste au planning.
	 * @param {Poste} poste - Le poste à ajouter.
	 */
	async addPoste(poste) {
		Backend.addPoste(this.festId, poste).catch(() => {
			alert('Une erreur est survenue lors de la création du poste');
			this.refreshPostesList();
		});

		this.postes.push(poste);
		this.renderPostes();
		this.refreshPostesList();
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
