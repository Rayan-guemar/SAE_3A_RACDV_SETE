import { Creneau } from './Creneau.js';
import { Poste } from './Poste.js';

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
	constructor(id, description, nbBenevole, poste, creneau) {
		this.id = id;
		this.description = description;
		this.nbBenevole = nbBenevole;
		this.poste = poste;
		this.creneau = creneau;
	}

	/**
	 * Vérifie si les créneau horaires de deux tâches se chevauchent.
	 * @param {Tache} tache
	 * @returns {boolean} Vrai si les créneaux horaires se chevauchent, faux sinon.
	 */
	overrides(tache) {
		const res = (this.creneau.debut < tache.creneau.fin && this.creneau.debut > this.creneau.debut) || (this.creneau.fin > tache.creneau.debut && this.creneau.fin < tache.creneau.fin);
		if (res) console.log('Comparing', this.creneau, 'with', tache.creneau);
		return res;
		// return (this.creneau.debut < tache.creneau.fin && this.creneau.debut > this.creneau.debut) || (this.creneau.fin > tache.creneau.debut && this.creneau.fin < tache.creneau.fin);
	}
}
