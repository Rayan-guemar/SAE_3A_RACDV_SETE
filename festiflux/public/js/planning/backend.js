import { Creneau } from './Creneau.js';
import { Poste } from './Poste.js';
import { Tache } from './Tache.js';

const URL_API = ''; // TODO: mettre l'URL de l'API

const samplePoste = new Poste(1, 'Accueil', 'Accueil des festivaliers');

const now = new Date('2023-10-25T07:00:00');
let now_plus_2 = new Date(now);
now_plus_2.setHours(now_plus_2.getHours() + 2);
const sampleCreneau = new Creneau(1, now, now_plus_2);

const sampleTache = new Tache(1, 2, samplePoste, sampleCreneau);

const taches = [sampleTache];
const postes = [samplePoste];

export const getTaches = () => {
	return taches;
};

export const getPostes = () => {
	return postes;
};
