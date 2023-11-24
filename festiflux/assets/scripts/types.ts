export interface Tache {
	id?: number;
	description: string;
	nbBenevole: number;
	lieu?: string;
	poste: Poste;
	creneau: Creneau;
}

export interface TacheCreateData {
	date_debut: Date;
	date_fin: Date;
	poste_id: string;
	lieu?: string;
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
	id: string;
	nom: string;
}

export interface Creneau {
	id?: number;
	debut: Date;
	fin: Date;
}

export interface Benevole {
	id: number;
	nom: string;
	prenom: string;
}

export interface Attribute {
	name: string;
	value: string;
}

export interface Input {
	id: string;
	name: string | null;
	label: string | null;
	value: string;
	type: string;
	attributes: Attribute[] | null;
}

export interface SubmitBtn {
	id: string;
	text: string;
	attributes: Attribute[] | undefined;
}
