import { dateDiff, encodedStr, getDateHours2Digits } from "./utils.js";
import { Poste } from "./Poste.js";
import { Backend } from "./Backend.js";
import { Creneau } from "./Creneau.js";
import { Tache } from "./Tache.js";

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
	 * @param {Tache[]} taches - Les créneaux du planning.
	 */
	constructor(festId, dateDebut, dateFin, isResponsableOrOrganisateur) {
		this.festId = festId;
		this.html = document.getElementById('planning');
		this.dateDebut = new Date(dateDebut);
		this.dateFin = new Date(dateFin);
		this.dateFin.setHours(23, 59, 59, 999);
		this.isResponsableOrOrganisateur = isResponsableOrOrganisateur;

    /**
     * Les postes associés au planning.
     * @type {Poste[]}
     */
    this.postes = [];

    /**
     * Les tâches associées au planning.
     * @type {Tache[]}
     */
    this.taches = [];

		if (this.isResponsableOrOrganisateur) {
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
		}

    this.addPostebtn = document.getElementById("add-poste-btn");
    this.addPosteForm = document.getElementById("add-poste");
    this.createPosteBtn = document.getElementById("create-poste-btn");
    this.createPosteInput = document.getElementById("poste-name");

    this.benevoleForm = document.getElementById("add-benevole");
    this.benevoleList = document.getElementById("benevoles-list");

    this.postesEl = document.querySelector(".postes");

    this.numberOfDays = dateDiff(this.dateDebut, this.dateFin).day + 1;

    this.days = this.html.querySelector(".days");

    this.dayNames = [
      "Dimanche",
      "Lundi",
      "Mardi",
      "Mercredi",
      "Jeudi",
      "Vendredi",
      "Samedi",
    ];
    this.monthNames = [
      "Janvier",
      "Février",
      "Mars",
      "Avril",
      "Mai",
      "Juin",
      "Juillet",
      "Août",
      "Septembre",
      "Octobre",
      "Novembre",
      "Décembre",
    ];

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
    const promises = [
      this.refreshPostesList(),
      this.refeshTachesList(),
      this.refreshBenevolesList(),
    ];
    await Promise.all(promises);

    document.getElementById("loader").style.display = "none";
    this.html.classList.remove("loading");
  }

  
	/**
	 * Ajoute les écouteurs d'événements pour les boutons et formulaires de la page de planification.
	 * @returns {void}
	 */
	addListener() {
		if (this.isResponsableOrOrganisateur) {
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

				const c = new Creneau(null, new Date(this.startCreneauxInput.value), new Date(this.endCreneauxInput.value));
				const t = new Tache(null, this.creneauDescription.value, nbBenevole, p, c);

				this.addTache(t);
				this.addCreneauxForm.classList.remove('visible');
				this.html.classList.remove('blurred');
			});
		}
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

    // Lorsque l'on clique sur le bouton fermé du formulaire de création de bénévole, on cache le formulaire et on enlève le flou de la page
    this.benevoleForm
      .querySelector(".close-btn")
      .addEventListener("click", () => {
        this.benevoleForm.classList.remove("visible");
        this.html.classList.remove("blurred");
      });
  }

  /**
   * Initialise les jours du planning en générant le code HTML correspondant.
   * @function
   * @returns {void}
   */
  initDays() {
    let html = "";
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

			for (const poste of this.postes) {
				const posteEl = document.querySelector(`[data-id="${poste.id}"]`);
				if (!posteEl) {
					console.error(`Aucun div de poste trouvé pour le poste ${poste}`);
					continue;
				}
				const color = poste.toColor();
				posteEl.style.backgroundColor = `rgb(${color.join(',')}, 0.1)`;
				posteEl.style.borderColor = `rgb(${color.join(',')})`;
			}
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
      alert("Une erreur est survenue lors de la création du poste");
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
    let dayWidth = this.days
      .querySelector(".day")
      .getBoundingClientRect().width;
    let scroll =
      this.days.scrollLeft - Math.floor(daysWidth / dayWidth) * dayWidth;
    if (scroll < 0) {
      scroll = 0;
    }
    this.days.scrollTo({
      left: scroll,
      behavior: "smooth",
    });
  }

  /**
   * Génère le HTML pour un jour donné.
   * @param {Date} date - La date du jour.
   * @returns {string} Le code HTML du jour.
   */
  dayHTML(date) {
    let lineBreaker = "";
    for (let i = 1; i <= 11; i++) {
      lineBreaker += `
                <div class="line-break" id="line-break-${i * 2}"></div>
            `;
    }
    let html = `
            <div class="day" data-date=${date.toISOString()}>
                <div class="heading">${this.dayNames[date.getDay()].substring(
                  0,
                  3
                )} ${date.getDate()} ${this.monthNames[
      date.getMonth()
    ].substring(0, 4)}.</div>
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
    let dayWidth = this.days
      .querySelector(".day")
      .getBoundingClientRect().width;
    let scroll =
      this.days.scrollLeft + Math.floor(daysWidth / dayWidth) * dayWidth;
    if (scroll > this.days.scrollWidth) {
      scroll = this.days.scrollWidth;
    }
    this.days.scrollTo({
      left: scroll,
      behavior: "smooth",
    });
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

    const dayDivs = document.querySelectorAll(".day");
    const dayDivsArray = [...dayDivs];

    for (const dayDiv of dayDivsArray) {
      const date = new Date(dayDiv.getAttribute("data-date"));
      dateToDayMap.set(date.toDateString(), dayDiv);
    }

    return dateToDayMap;
  };

	/**
	 *
	 * @param {Tache} tache
	 * @param {HTMLDivElement} dayDiv
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
	 *
	 * @param {Tache} tache
	 * @param {HTMLDivElement} dayDiv
	 */
	renderOneTache = (t, dayDiv) => {
		const taskDiv = document.createElement('div');
		taskDiv.classList.add('task');
		taskDiv.innerHTML = `
            <div class="name">${encodedStr(t.poste.nom)}</div>
            <div class="creneau">${encodedStr(`${getDateHours2Digits(t.creneau.debut)} - ${getDateHours2Digits(t.creneau.fin)}`)}</div>
        `;

		taskDiv.style.top = `${(t.creneau.debut.getHours() / 24) * 100}%`;
		taskDiv.style.height = `${((t.creneau.fin.getHours() - t.creneau.debut.getHours()) / 24) * 100}%`;
		taskDiv.style.borderColor = `rgb(${t.poste.toColor().join(',')})`;
		taskDiv.style.backgroundColor = `rgb(${t.poste.toColor().join(',')}, 0.1)`;
		taskDiv.style.color = `rgb(${t.poste.toColor().join(',')})`;
		dayDiv.appendChild(taskDiv);
	};

	/**
	 *
	 * @param {Tache[]} taches
	 * @param {HTMLDivElement} dayDiv
	 */
	renderMultipleTaches = (taches, dayDiv) => {
		for (let i = 0; i < taches.length; i++) {
			const t = taches[i];
			const taskDiv = document.createElement('div');
			taskDiv.classList.add('task');
			taskDiv.innerHTML = `
            <div class="name">${encodedStr(t.poste.nom)}</div>
            <div class="creneau">${encodedStr(`${getDateHours2Digits(t.creneau.debut)} - ${getDateHours2Digits(t.creneau.fin)}`)}</div>
        `;
		taskDiv.style.top = `${
			((t.creneau.debut.getHours() * 60 + t.creneau.debut.getMinutes()) /
			(24 * 60)) *
			100
		}%`;
		taskDiv.style.height = `${
			((t.creneau.fin.getHours() * 60 +
			t.creneau.fin.getMinutes() -
			(t.creneau.debut.getHours() * 60 + t.creneau.debut.getMinutes())) /
			(24 * 60)) *
			100
		}%`;
			taskDiv.style.width = `calc(${100 / taches.length}% - 4px)`;
			taskDiv.style.margin = `0 2px`;
			taskDiv.style.left = `${(100 / taches.length) * i}%`;
			taskDiv.style.transform = `translateX(0%)`;
			taskDiv.style.borderColor = `rgb(${t.poste.toColor().join(',')})`;
			taskDiv.style.backgroundColor = `rgb(${t.poste.toColor().join(',')}, 0.1)`;
			taskDiv.style.color = `rgb(${t.poste.toColor().join(',')})`;
			taskDiv.addEventListener("click", () => {
				document.getElementById("tache-id").value = t.id;
				this.benevoleForm.classList.add("visible");
				this.html.classList.add("blurred");
			});
			dayDiv.appendChild(taskDiv);
		}
	};

	renderTaches = () => {
		const sortedTaches = this.sortTachesByOverriding();
		const dateToDayMap = this.getDateToDayMapping();

		console.log(sortedTaches);

		for (const d of dateToDayMap.values()) {
			[...d.getElementsByClassName('task')].forEach(t => t.remove());
		}

		for (const taches of sortedTaches) {
			const date = new Date(taches[0].creneau.debut);
			const dayDiv = dateToDayMap.get(date.toDateString());
			if (!dayDiv) {
				console.error(`Aucun div de jour trouvé pour la date ${date}`);
				continue;
			}
			this.renderMultipleTaches(taches, dayDiv);
		}
	};

	/**
	 * Affiche toutes les tâches dans le planning.
	 */
	refeshTachesList = async () => {
		this.taches = await Backend.getTaches(this.festId);
		this.renderTaches();
	};
		

  /**
   * Affiche toutes les tâches dans le planning.
   */
  refeshTachesList = async () => {
    this.taches = await Backend.getTaches(this.festId);
	console.log(this.renderTaches);
    this.renderTaches();
  };	

  /**
   * Ajoute une tache au planning.
   * @param {Tache} tache
   */
  async addTache(tache) {
    this.taches.push(tache);
    this.renderTaches();
    try {
      await Backend.addTache(this.festId, tache);
    } catch (error) {
      alert("Une erreur est survenue lors de la création de la tâche");
      this.taches = this.taches.filter((t) => t.id !== tache.id);
      this.renderTaches();
    }
    this.refeshTachesList();
  }

  /**
   * Génère le code HTML pour les Benevoles.
   * @function
   * @returns {void}
   */
  renderBenevole() {
    if (this.benevoles.length === 0) {
      this.benevoleList.innerHTML = `
				<div class="no-benevole">Aucun bénévole trouvé pour ce festival</div>
			`;
    } else {
      this.benevoleList.innerHTML = this.benevoles
        .map(
          (benevole) => `<div class="benevole">
            	<div class="name">${encodedStr(benevole.nom)} ${encodedStr(
            benevole.prenom
          )}</div>
            	<input class='benevole-checkbox' type="checkbox" name="benevole" id="${
                benevole.id
              }" value="${benevole.id}">
			</div>
        `
        )
        .join("");
    }

	this.handleCheckboxChange();
  }

  refreshBenevolesList = async () => {
    this.benevoles = await Backend.fetchBenevoles(this.festId);
    this.renderBenevole();
  };

  /**
   * Ajoute un Benevole à une tache.
   * @param {Benevole} benevole
   * @param {Tache} tache
   */
  async addBenevole(benevole, tache) {
    try {
      await Backend.addBenevole(this.festId, benevole, tache);
    } catch (error) {
      alert("Une erreur est survenue lors de l'ajout du bénévole");
    }
    this.refreshBenevolesList();
  }

  handleCheckboxChange = async () => {
    const checkboxs = document.getElementsByClassName("benevole-checkbox");

    [...checkboxs].forEach((checkbox) => {
      const userId = checkbox.id;
      checkbox.addEventListener("change", async function () {
        const isChecked = checkbox.checked;
        const URL = Routing.generate(
          isChecked ? "app_user_task_add" : "app_user_task_remove",
          { id: userId, idTask: document.getElementById("tache-id").value }
        );
        try {
          await fetch(URL);
        } catch (error) {
          console.log(error);
          checkbox.checked = !isChecked;
        }
      });
    });
  };

  sortTachesByOverriding = () => {
	/**
	 * @type {Tache[][]}
	 */
	const overridingTaches = [];

	for (const t of this.taches) {
		const a = overridingTaches.find(ts => {
			return ts.some(_t => _t.overrides(t));
		});
		if (a) {
			a.push(t);
		} else {
			overridingTaches.push([t]);
		}
	}
	return overridingTaches;
};
}
