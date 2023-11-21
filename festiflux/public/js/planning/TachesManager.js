import { Backend } from './Backend.js';
import { Tache } from './Entity/Tache.js';
import { SortedTachesList } from './SortedTachesList.js';

export class TachesManager {
	/**
	 *
	 * @param {Tache[]} taches
	 */
	constructor(taches = []) {
		this.taches = taches;
		this.sortedTaches = new SortedTachesList(taches);
	}

	/**
	 *
	 * @param {Tache} tache
	 */
	async addTache(tache) {
		this.sortedTaches.addTache(tache);
		try {
			await Backend.addTache(tache);
			await this.update();
		} catch (error) {
			this.sortedTaches.removeTache(tache.id);
			console.error('Error adding task:', error);
		}
	}

	async update() {
		this.taches = await Backend.fetchTaches();
		this.sortedTaches.update(this.taches);
	}

	onSortedTachesUpdate(cb) {
		this.sortedTaches.onUpdate(cb);
	}
}
