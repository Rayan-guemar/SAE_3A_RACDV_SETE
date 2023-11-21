import { Creneau } from './Creneau.js';
import { Poste } from './Poste.js';
import { compareDates, getDateHours2Digits } from '../utils.js';
/**
 * Classe représentant une tâche à effectuer lors d'un événement.
 * @class
 */
export class Tache {
	/**
	 * Crée une instance de Tache.
	 * @constructor
	 * @param {number} id - L'identifiant de la tâche.
	 * @param {string} description - La description de la tâche.
	 * @param {number} nbBenevole - Le nombre de bénévoles nécessaires pour effectuer la tâche.
	 * @param {Poste} poste - Le poste associé à la tâche.
	 * @param {Creneau} creneau - Le créneau horaire associé à la tâche.
	 */
	constructor(id, description, nbBenevole, poste, creneau, benevoles = []) {
		this.id = id;
		this.description = description;
		this.nbBenevole = nbBenevole;
		this.poste = poste;
		this.creneau = creneau;
		this.benevoles = benevoles;
	}

	/**
	 * Vérifie si les créneau horaires de deux tâches se chevauchent.
	 * @param {Tache} tache
	 * @returns {boolean} Vrai si les créneaux horaires se chevauchent, faux sinon.
	 */
	overrides(tache) {
		return this.creneau.overrides(tache.creneau);
	}
}

const sampleTache = new Tache(1, 'Accueil', 2, new Poste(1, 'Accueil'), new Creneau(1, new Date('2020-01-01T09:00:00'), new Date('2020-01-01T11:00:00')));
const sampleTache2 = new Tache(2, 'Accueil', 2, new Poste(1, 'Accueil'), new Creneau(1, new Date('2020-01-01T10:00:00'), new Date('2020-01-01T12:00:00')));
const sampleTache3 = new Tache(3, 'Accueil', 2, new Poste(1, 'Accueil'), new Creneau(1, new Date('2020-01-01T11:00:00'), new Date('2020-01-01T14:00:00')));
console.log(sampleTache.overrides(sampleTache2));
console.log(sampleTache2.overrides(sampleTache));
console.log(sampleTache.overrides(sampleTache3));
console.log(sampleTache3.overrides(sampleTache));
console.log(sampleTache.overrides(sampleTache));
