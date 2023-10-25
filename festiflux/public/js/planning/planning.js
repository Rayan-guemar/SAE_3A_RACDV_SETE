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
        })

        this.postes.querySelector('.close-btn').addEventListener('click', () => {
            this.postes.classList.remove('visible');
            this.html.classList.remove('blurred');
        })

        this.addPostebtn.addEventListener('click', () => {
            this.addPosteForm.classList.add('visible');
        })
    }

    addPoste(poste) {
        this.postes.appendChild(poste.html);
    }

}

let planning = new Planning(document.getElementById('planning'), Date.parse('2019-07-01'), Date.parse('2019-07-07'));