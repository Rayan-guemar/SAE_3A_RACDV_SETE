import { compareDates } from './utils.js';

/**
 * Représente un créneau horaire.
 * @class
 */
export class Creneau {
	/**
	 * Crée une instance de Creneau.
	 * @constructor
	 * @param {number} id - L'identifiant du créneau.
	 * @param {Date} debut - La date de début du créneau.
	 * @param {Date} fin - La date de fin du créneau.
	 */
	constructor(id, debut, fin) {
		this.id = id;
		this.debut = new Date(debut);
		this.fin = new Date(fin);
	}

	/**
	 * Vérifie si deux créneaux sont égaux.
	 * @param {Creneau} creneau - Le créneau à comparer.
	 * @returns {boolean} Vrai si les créneaux sont égaux, faux sinon.
	 */
	equals(creneau) {
		return this.debut.getTime() === creneau.debut.getTime() && this.fin.getTime() === creneau.fin.getTime();
	}

	/**
	 * Vérifie si deux créneaux se chevauchent.
	 * @param {Creneau} creneau - Le créneau à comparer.
	 * @returns {boolean} Vrai si les créneaux se chevauchent, faux sinon.
	 */
	overrides(creneau) {
		return (
			this.equals(creneau) ||
			(this.debut.getTime() > creneau.debut.getTime() && this.debut.getTime() < creneau.fin.getTime()) ||
			(this.fin.getTime() > creneau.debut.getTime() && this.fin.getTime() < creneau.fin.getTime())
		);
	}
}
