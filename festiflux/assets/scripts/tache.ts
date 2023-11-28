import { Tache } from './types';

export const sortTachesByOverriding = (taches: Tache[]) => {
	const overridingTaches = [] as Tache[][];

	for (const t of taches) {
		const a = overridingTaches.find(ts => ts.some(_t => overrides(_t, t)));
		if (a) a.push(t);
		else overridingTaches.push([t]);
	}
	const res = [] as { tache: Tache; position: number; total: number }[];

	for (let i = 0; i < overridingTaches.length; i++) {
		const taches = overridingTaches[i];
		for (let j = 0; j < taches.length; j++) {
			const tache = taches[j];
			res.push({ tache: tache, position: j + 1, total: taches.length });
		}
	}

	return res;
};

const overrides = (tache1: Tache, tache2: Tache) => {
	const c1 = tache1.creneau;
	const c2 = tache2.creneau;
	if (c1.debut.getTime() == c2.debut.getTime() && c1.fin.getTime() == c2.fin.getTime()) return true;
	if (c1.debut.getTime() >= c2.debut.getTime() && c1.debut.getTime() <= c2.fin.getTime()) return true;
	if (c1.fin.getTime() >= c2.debut.getTime() && c1.fin.getTime() <= c2.fin.getTime()) return true;
};
