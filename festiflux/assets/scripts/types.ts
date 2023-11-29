export interface Tache {
	id?: number;
	description: string;
	nbBenevole: number;
	benevoleAffecte?: number;	
	lieu?: string;
	poste: Poste;
	creneau: Creneau;
	benevoles?: Benevole[];
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
	id: string | null;
	nom: string;
	description: string;
	couleur?: string;
}

export interface Creneau {
	id?: number;
	debut: Date;
	fin: Date;
}

export interface Lieu {
	nom: string;
	adresse: string;
}

export interface Benevole {
	id: number;
	nom: string;
	prenom: string;
}

export interface IndispoCreateData {
	id: number;
	userID: number;
	festID: number;
	dateDebut: Date;
	dateFin: Date;
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

