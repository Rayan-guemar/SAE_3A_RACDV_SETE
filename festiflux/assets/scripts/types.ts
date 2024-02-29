export type ID = string | number;

export interface Tache {
	id?: ID;
	description: string;
	nbBenevole: number;
	benevoleAffecte?: number;
	lieu?: string;
	poste: Poste;
	creneau: Creneau;
	benevoles?: ID[];
}

export interface TacheCreateData {
	date_debut: Date;
	date_fin: Date;
	poste_id: string;
	lieu: string;
	adresse: string;
	description: string;
	nombre_benevole: number;
}

export interface Festival {
	festID: number;
	title: string;
	dateDebut: Date;
	dateFin: Date;
	isOrgaOrResp: boolean;
}

export interface Poste {
	id?: ID;
	nom: string;
	description: string;
	couleur?: string;
}

export interface Creneau {
	id?: ID;
	debut: Date;
	fin: Date;
}

export interface Lieu {
	nom: string;
	adresse: string;
}

export interface Benevole {
	id: ID;
	nom: string;
	prenom: string;
	preferences: Preference[];
	indisponibilites: Creneau[];
}

export interface Preference {
	poste: ID;
	degree: number;
}

export interface SelectOption {
	value: ID;
	label: string;
}
export interface Plage extends Creneau {
	id: ID;
}

export interface Indisponibilite extends Creneau {
	id: ID;
}
