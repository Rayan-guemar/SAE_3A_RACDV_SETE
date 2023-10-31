import { Creneau } from './Creneau.js';
import { Poste } from './Poste.js';
import { Tache } from './Tache.js';

/**
 * Classe représentant un backend pour effectuer des requêtes HTTP.
 */
export class Backend {
	/**
	 * Effectue une requête HTTP à l'URL spécifiée.
	 * @param {string} URL - L'URL à laquelle effectuer la requête.
	 * @param {BodyInit | undefined} body - Le corps de la requête.
	 * @returns {Promise<any>} - Une promesse qui résout avec les données de la réponse.
	 */
	static async #fetch(URL, body) {
		const response = await fetch(URL, body);
		if (!response.ok) {
			throw new Error(await response.text());
		}
		let data;
		try {
			data = await response.json();
		} catch (error) {
			data = {};
		}
		return data;
	}

	/**
	 * Effectue une requête HTTP GET à l'URL spécifiée.
	 * @param {string} URL - L'URL à laquelle effectuer la requête.
	 * @returns {Promise<any>} - Une promesse qui résout avec les données de la réponse.
	 */
	static async #get(URL) {
		return await this.#fetch(URL);
	}

	/**
	 * Effectue une requête HTTP POST à l'URL spécifiée avec le corps spécifié.
	 * @param {string} URL - L'URL à laquelle effectuer la requête.
	 * @param {BodyInit} body - Le corps de la requête.
	 * @returns {Promise<any>} - Une promesse qui résout avec les données de la réponse.
	 */
	static async #post(URL, body) {
		const response = await this.#fetch(URL, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify(body)
		});

		return response;
	}

	/**
	 * Récupère les postes liés à un festival spécifique.
	 * @param {number} festivalId - L'identifiant du festival.
	 * @returns {Promise<Poste[]>} - Une promesse qui résout avec les données des postes.
	 */
	static async fetchPostes(festivalId) {
		const URL = Routing.generate('app_festival_all_poste', { id: festivalId });
		const res = await Backend.#get(URL);
		return res.postes?.map(poste => new Poste(poste.id, poste.nom)) || [];
	}
	/**
	 * Récupère les tâches liées à un festival spécifique.
	 * @param {number} festivalId - L'identifiant du festival.
	 * @param {Poste} poste - Le poste.
	 * @returns {Promise<any>} - Une promesse qui résout avec les données des tâches.
	 */
	static addPoste(festivalId, poste) {
		const URL = Routing.generate('app_festival_create_poste', { id: festivalId });
		return Backend.#post(URL, poste);
	}

	/**
	 * Ajoute une tâche à un festival spécifique.
	 * @param {number} festivalId - L'identifiant du festival.
	 * @param {Tache} tache - Les informations de la tâche à ajouter.
	 * @returns {Promise<any>} - Une promesse qui résout avec les données de la réponse.
	 */
	static async addTache(festivalId, tache) {
		const body = {
			dateDebut: tache.creneau.debut.toISOString(),
			dateFin: tache.creneau.fin.toISOString(),
			poste_id: tache.poste.id,
			lieu: tache.lieu,
			description: tache.description,
			nombre_benevole: tache.nbBenevole
		};
		const URL = Routing.generate('app_festival_add_tache', { id: festivalId });
		const res = await Backend.#post(URL, body);
		console.log('response', res);
	}

	/**
	 *
	 * @param {string} festivalId
	 * @returns {Promise<Tache[]>} - Une promesse qui résout avec les données des tâches.
	 */
	static async getTaches(festivalId) {
		const URL = Routing.generate('app_festival_tache', { id: festivalId });
		const data = await Backend.#get(URL);
		const res = data.map(o => new Tache(o.id, o.description, o.nombre_benevole, new Poste(o.poste_id, o.poste_nom), new Creneau(null, new Date(o.date_debut?.date), new Date(o.date_fin?.date))));
		return res;
	}
}
