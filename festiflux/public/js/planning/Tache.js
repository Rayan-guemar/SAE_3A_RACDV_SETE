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
	constructor(id, nbBenevole, poste, creneau) {
		this.id = id;
		this.nbBenevole = nbBenevole;
		this.poste = poste;
		this.creneau = creneau;
	}
}
