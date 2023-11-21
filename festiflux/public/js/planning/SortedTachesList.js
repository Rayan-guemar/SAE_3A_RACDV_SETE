import { Tache } from './Entity/Tache.js';

export class SortedTachesList {
	/**
	 *
	 * @param {Tache[]} taches
	 */
	constructor(taches = []) {
		this.taches = SortedTachesList.sortTachesByOverriding(taches);
	}

	/**
	 * Trie les tâches par chevauchement de créneaux horaires.
	 * @param {Tache[]} taches - Un tableau de tâches.
	 * @returns {Tache[][]} Un tableau de tâches triées.
	 */
	static sortTachesByOverriding = taches => {
		/**
		 * @type {Tache[][]}
		 */
		const overridingTaches = [];

		console.log('TACHES: ' + taches);

		for (const t of taches) {
			const a = overridingTaches.find(ts => ts.some(_t => _t.overrides(t)));
			if (a) a.push(t);
			else overridingTaches.push([t]);
		}
		return overridingTaches;
	};

	update(taches) {
		this.taches = SortedTachesList.sortTachesByOverriding(taches);
		this.onUpdateCallback();
	}

	addTache(tache) {
		const a = this.taches.find(ts => ts.some(_t => _t.overrides(tache)));
		if (a) a.push(tache);
		else this.taches.push([t]);
		this.onUpdateCallback();
	}

	removeTache(tache) {
		for (const t of this.taches) {
			let a = this.taches.find(ts => ts.some(_t => _t == tache));
			if (a.length == 0) this.taches.filter(ts => ts != a);
			else a = a.filter(t => t.id != tache);
		}
		this.onUpdateCallback();
	}

	onUpdate(cb) {
		this.onUpdateCallback = cb;
	}
}
