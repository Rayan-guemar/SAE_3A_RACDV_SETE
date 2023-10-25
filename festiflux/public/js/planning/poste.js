/**
 * Représente un objet Poste.
 * @class
 */
export class Poste {
	/**
	 * Crée un nouvel objet Poste.
	 * @constructor
	 * @param {number} id - L'ID du poste.
	 * @param {string} nom - Le nom du poste.
	 */
	constructor(id, nom) {
		this.id = id;
		this.nom = nom;
	}
}
