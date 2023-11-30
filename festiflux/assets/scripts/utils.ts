/**
 * Encode une chaîne de caractères en remplaçant les caractères spéciaux par leur équivalent HTML.
 * @param s - La chaîne de caractères à encoder.
 * @returns La chaîne de caractères encodée.
 */
export const encodedStr = (s: string): string => (s + '').replace(/[\u00A0-\u9999<>\&]/g, i => '&#' + i.charCodeAt(0) + ';');

/**
 * Convertit une date en format "xx h x" (ex: 12 h 30).
 * @param d - La date à convertir.
 * @returns La date en format "xx h x".
 */
export const getDateHours2Digits = (d: Date): string => {
	d = new Date(d);
	return (
		d.getHours().toLocaleString('en-US', {
			minimumIntegerDigits: 2,
			useGrouping: false
		}) +
		' h ' +
		d.getMinutes().toLocaleString('en-US', { minimumIntegerDigits: 2, useGrouping: false })
	);
};

/**
 * Pause l'exécution du code pendant un certain nombre de millisecondes.
 * @param ms - Le nombre de millisecondes à attendre.
 * @returns Une promesse qui se résout après le délai spécifié.
 */
export const pause = (ms: number): Promise<void> => new Promise(resolve => setTimeout(resolve, ms));

export const dateDiff = (date1: Date, date2: Date): { sec: number; min: number; hour: number; day: number } => {
	const diff = {} as { sec: number; min: number; hour: number; day: number }; // Initialisation du retour
	let tmp = date2.getTime() - date1.getTime();

	tmp = Math.floor(tmp / 1000); // Nombre de secondes entre les 2 dates
	diff.sec = tmp % 60; // Extraction du nombre de secondes

	tmp = Math.floor((tmp - diff.sec) / 60); // Nombre de minutes (partie entière)
	diff.min = tmp % 60; // Extraction du nombre de minutes

	tmp = Math.floor((tmp - diff.min) / 60); // Nombre d'heures (entières)
	diff.hour = tmp % 24; // Extraction du nombre d'heures

	tmp = Math.floor((tmp - diff.hour) / 24); // Nombre de jours restants
	diff.day = tmp;

	return diff;
};

/**
 * Compare deux dates. Renvoie 0 si elles sont égales, 1 si la première est plus grande que la seconde, -1 sinon.
 * @param date1
 * @param date2
 * @returns
 */
export const compareDates = (date1: Date, date2: Date): number => {
	if (date1.getFullYear() > date2.getFullYear()) return 1;
	else if (date1.getFullYear() < date2.getFullYear()) return -1;
	else if (date1.getMonth() > date2.getMonth()) return 1;
	else if (date1.getMonth() < date2.getMonth()) return -1;
	else if (date1.getDate() > date2.getDate()) return 1;
	else if (date1.getDate() < date2.getDate()) return -1;
	else return 0;
};

export const hashCode = (s: string): number => {
	let hash = 0;
	let i, chr;
	if (s.length === 0) return hash;
	for (i = 0; i < s.length; i++) {
		chr = s.charCodeAt(i);
		hash = (hash << 5) - hash + chr;
		hash |= 0;
	}
	return hash;
};

export const generate_route = (route_name: string) => {};

export const getDateFromLocale = (date_local: string | Date) => {
	const date = new Date(date_local);
	const res = date.getTime() - date.getTimezoneOffset() * 60 * 1000;
	return new Date(res);
};

export const hexToBrighterHex = (hex: string, percent: number = 0.1): string => {
	hex = hex.replace(/^\s*#|\s*$/g, '');

	let r = parseInt(hex.substring(0, 2), 16),
		g = parseInt(hex.substring(2, 4), 16),
		b = parseInt(hex.substring(4, 6), 16);

	r += Math.round((255 - r) * (1 - percent));
	g += Math.round((255 - g) * (1 - percent));
	b += Math.round((255 - b) * (1 - percent));

	return 'rgb(' + Math.min(r, 255) + ',' + Math.min(g, 255) + ',' + Math.min(b, 255) + ')';
};

export const displayHoursMinutes = (date: Date): string => {
	const conv = (e: number) => (e < 10 ? `0${e}` : e);
	return `${conv(date.getHours())}h${conv(date.getMinutes())}`;
};

export const getDateForInputAttribute = (date: Date | string): string => {
	return new Date(date).toISOString().split('.')[0];
};
