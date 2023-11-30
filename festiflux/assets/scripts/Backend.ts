import { Benevole, Creneau, Poste, Tache, TacheCreateData, IndispoCreateData, ID } from './types';
import { getDateFromLocale } from './utils';

export class Backend {
	static async #fetch(URL: string, body?: RequestInit): Promise<any> {
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
	static async #get(URL: string) {
		return await this.#fetch(URL);
	}

	/**
	 * Effectue une requête HTTP POST à l'URL spécifiée avec le corps spécifié.
	 * @param {string} URL - L'URL à laquelle effectuer la requête.
	 * @param {BodyInit} body - Le corps de la requête.
	 * @returns {Promise<any>} - Une promesse qui résout avec les données de la réponse.
	 */
	static async #post(URL: string, body: RequestInit) {
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
	static async getPostes(festivalId: ID): Promise<Poste[]> {
		// @ts-ignore
		const URL = Routing.generate('app_festival_all_poste', { id: festivalId });
		const res = await Backend.#get(URL);
		return (
			res.postes?.map((poste: Poste) => ({
				id: poste.id,
				nom: poste.nom,
				description: poste.description,
				couleur: poste.couleur
			})) || ([] as Poste[])
		);
	}

	/**
	 * Ajoute un benevole à une tache spécifique.
	 * @param {Object} benevole - Les informations du benevole à ajouter.
	 * @param {Tache} tache - Les informations de la tache.
	 * @returns {Promise<any>} - Une promesse qui résout avec les données de la réponse.
	 */
	static async addBenevole(benevole: Benevole, tache: Tache) {
		// @ts-ignore
		const URL = Routing.generate('app_user_task_add', {
			id: benevole.id,
			idTask: tache.id
		});
		// @ts-ignore
		await Backend.#post(URL, '');
	}

	static async removeBenevole(benevole: Benevole, tache: Tache) {
		// @ts-ignore
		const URL = Routing.generate('app_user_task_remove', {
			id: benevole.id,
			idTask: tache.id
		});
		// @ts-ignore
		await Backend.#post(URL, '');
	}

	/**
	 * Ajoute un poste à un festival spécifique.
	 * @param {number} festivalId - L'identifiant du festival.
	 * @param {Poste} poste - Les informations du poste à ajouter.
	 * @returns {Promise<any>} - Une promesse qui résout avec les données de la réponse.
	 */
	static addPoste(festivalId: ID, poste: Poste) {
		// @ts-ignore
		const URL = Routing.generate('app_festival_create_poste', {
			id: festivalId
		});
		return Backend.#post(URL, poste as RequestInit);
	}

	static updatePoste(festivalId: ID, poste: Poste) {
		// @ts-ignore
		const URL = Routing.generate('app_festival_edit_poste', {
			id: festivalId,
			idPoste: poste.id
		});
		return Backend.#post(URL, poste as RequestInit);
	}

	static deletePoste(festivalId: ID, poste: Poste) {
		// @ts-ignore
		const URL = Routing.generate('app_festival_delete_poste', {
			id: festivalId,
			idPoste: poste.id
		});
		return Backend.#get(URL);
	}

	/**
	 * Ajoute une tâche à un festival spécifique.
	 * @param {number} festivalId - L'identifiant du festival.
	 * @param {Tache} tache - Les informations de la tâche à ajouter.
	 * @returns {Promise<number>} - Une promesse qui résout avec les données de la réponse.
	 */
	static async addTache(festivalId: ID, tache: TacheCreateData) {
		const body = {
			...tache,
			date_debut: getDateFromLocale(tache.date_debut).toISOString(),
			date_fin: getDateFromLocale(tache.date_fin).toISOString()
		};
		// @ts-ignore
		const URL = Routing.generate('app_festival_add_tache', { id: festivalId });
		return await Backend.#post(URL, body as RequestInit);
	}

	/**
	 *
	 * @param {string} festivalId
	 * @param creneau
	 */
	static async addHeureDepartFin(festivalId: ID, creneau: Creneau) {
		// @ts-ignore
		const URL = Routing.generate('app_festival_add_DebutFinDay', { id: festivalId });
		await Backend.#post(URL, creneau as RequestInit);
	}

	static async getPlagesHoraires(festivalId: ID): Promise<Creneau[]> {
		// @ts-ignore
		const URL = Routing.generate('app_festival_get_DebutFinDay', { id: festivalId });
		const data = await Backend.#get(URL);

		const res = [...data].map<Creneau>((o: any) => ({
			id: o.id,
			debut: new Date(o.debut?.date),
			fin: new Date(o.fin?.date)
		}));

		return res;
	}

	static async addIndispo(festivalId: ID, creneau: Creneau) {
		// @ts-ignore
		const URL = Routing.generate('app_festival_add_disponibilities', { id: festivalId });
		return await Backend.#post(URL, creneau as RequestInit);
	}

	/**
	 *
	 * @param {string} festivalId
	 * @returns {Promise<Tache[]>} - Une promesse qui résout avec les données des tâches.
	 */
	static async getTaches(festivalId: ID): Promise<Tache[]> {
		// @ts-ignore
		const URL = Routing.generate('app_festival_tache', { id: festivalId });
		const data = await Backend.#get(URL);
		
		const res = [...data].map(
			(o: any) =>
				({
					id: o.id,
					description: o.description,
					nbBenevole: o.nombre_benevole,
					benevoleAffecte: o.benevole_affecte,
					lieu: o.lieu,
					poste: { id: o.poste_id, nom: o.poste_nom, description: o.poste_description, couleur: o.poste_couleur } as Poste,
					creneau: { debut: new Date(o.date_debut?.date), fin: new Date(o.date_fin?.date) },
					benevoles: o.benevoles
				} as Tache)
		);

		return res;
	}

	static async getBenevoles(festivalId: ID): Promise<Benevole[]> {
		// @ts-ignore
		const URL = Routing.generate('app_festival_all_benevole', { id: festivalId });
		const data = await Backend.#get(URL);

		const res = [...data].map(
			(o: any) =>
				({
					id: o.id,
					nom: o.nom,
					prenom: o.prenom,
					preferences: o.preferences
				} as Benevole)
		);

		return res;
	}

	static async saveBenevole(tacheId: ID, affected: ID[], unaffected: ID[]) {
		// @ts-ignore
		const URL = Routing.generate('app_benevole_save', { id: tacheId });
		return await Backend.#post(URL, {
			affected: affected,
			unaffected: unaffected
		} as RequestInit);
	}

	static async getICS(festId: ID): Promise<any> {
		// @ts-ignore
		const URL = Routing.generate('app_send_icsFile', { idFest: festId });
		try {
			await fetch(URL);
			alert('Votre allez recevoir un mail avec en pj le fichier ics.');
		} catch (error) {
			console.log(error);
		}
	}

}
