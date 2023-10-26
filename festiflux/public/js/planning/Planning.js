import { dateDiff } from './utils.js';

/**
 * Classe représentant un planning.
 */
export class Planning {
    /**
     * Crée une instance de la classe Planning.
     * @param {Date} dateDebut - La date de début du planning.
     * @param {Date} dateFin - La date de fin du planning.
     * @param {Array} postes - Les postes associés au planning.
     * @param {Array} creneaux - Les créneaux du planning.
     */
    constructor(dateDebut, dateFin, postes, creneaux) {
        this.html = document.getElementById('planning');
        this.dateDebut = dateDebut;
        this.dateFin = dateFin;
        this.postes = postes || [];
        this.creneaux = creneaux || [];

        this.addCreneauxBtn = document.getElementById('add-creneaux');
        this.addCreneauxForm = document.getElementById('add-creneaux');

        this.postesBtn = document.getElementById('poste-btn');
        this.postes = document.getElementById('postes');

        this.addPostebtn = document.getElementById('add-poste-btn');
        this.addPosteForm = document.getElementById('add-poste');

        this.numberOfDays = dateDiff(this.dateDebut, this.dateFin).day + 1;

        this.days = this.html.querySelector('.days');

        this.dayNames = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        this.monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août','Septembre', 'Octobre', 'Novembre', 'Décembre'];

        this.initDays();
        this.addListener();

        document.getElementById('loader').style.display = 'none';
        this.html.classList.remove('loading');
    }

    /**
     * Ajoute des écouteurs d'événements aux éléments de la page.
     */
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

    /**
     * Initialise les jours du planning.
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
     * Ajoute un poste au planning.
     * @param {Poste} poste - Le poste à ajouter.
     */
    addPoste(poste) {
        this.postes.appendChild(poste.html);
    }

    /**
     * Fait défiler les jours vers la gauche.
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
        })
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
                <div class="line-break" id="line-break-${ i * 2 }"></div>
            `;
        }
        let html = `
            <div class="day" data-date=${date.toISOString()}>
                <div class="heading">${ this.dayNames[date.getDay()].substring(0, 3) } ${ date.getDate() } ${ this.monthNames[date.getMonth()].substring(0, 4) }.</div>
                ${ lineBreaker }
            </div>
        `;
        
        return html;
    }
    
    /**
     * Fait défiler les jours vers la droite.
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
        })
    }
}