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
		this.debut = debut;
		this.fin = fin;
	}
}
