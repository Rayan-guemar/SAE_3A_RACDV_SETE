/**
 * Encode une chaîne de caractères en remplaçant les caractères spéciaux par leur équivalent HTML.
 * @param {string} s - La chaîne de caractères à encoder.
 * @returns {string} La chaîne de caractères encodée.
 */
export const encodedStr = s => (s + '').replace(/[\u00A0-\u9999<>\&]/g, i => '&#' + i.charCodeAt(0) + ';');

/**
 * Convertit une date en format "xx h x" (ex: 12 h 30).
 * @param {Date} d - La date à convertir.
 * @returns {string} La date en format "xx h x".
 */
export const getDateHours2Digits = d => {
	return d.getHours().toLocaleString('en-US', { minimumIntegerDigits: 2, useGrouping: false });
};

export const dateDiff = (date1, date2) => {
	var diff = {}; // Initialisation du retour
	var tmp = date2 - date1;

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
