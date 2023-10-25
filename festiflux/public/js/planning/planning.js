function dateDiff(date1, date2){
	var diff = {}							// Initialisation du retour
	var tmp = date2 - date1;

	tmp = Math.floor(tmp/1000);             // Nombre de secondes entre les 2 dates
	diff.sec = tmp % 60;					// Extraction du nombre de secondes

	tmp = Math.floor((tmp-diff.sec)/60);	// Nombre de minutes (partie entière)
	diff.min = tmp % 60;					// Extraction du nombre de minutes

	tmp = Math.floor((tmp-diff.min)/60);	// Nombre d'heures (entières)
	diff.hour = tmp % 24;					// Extraction du nombre d'heures
	
	tmp = Math.floor((tmp-diff.hour)/24);	// Nombre de jours restants
	diff.day = tmp;
	
	return diff;
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