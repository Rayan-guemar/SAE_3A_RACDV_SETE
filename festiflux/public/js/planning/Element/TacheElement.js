import { Tache } from '../Entity/Tache.js';

export class TacheElement {
	/**
	 * @param {Tache} tache
	 */
	constructor(tache, position = 1, total = 1) {
		this.tache = tache;
		this.element;
		this.position = position;
		this.total = total;
	}

	/**
	 * @returns {HTMLDivElement}
	 */
	htmlNode() {
		this.element = document.createElement('div');
		this.element.classList.add('task');
		this.element.setAttribute('data-id', this.tache.id);
		this.element.innerHTML = `
            <div class="name">${encodedStr(this.tache.poste.nom)}</div>
            <div class="tache">${encodedStr(`${getDateHours2Digits(this.tache.creneau.debut)} - ${getDateHours2Digits(this.tache.creneau.fin)}`)}</div>
        `;
		this.element.style.top = `${((this.tache.creneau.debut.getHours() * 60 + this.tache.creneau.debut.getMinutes()) / (24 * 60)) * 100}%`;
		this.element.style.height =
			((this.tache.creneau.fin.getHours() * 60 + this.tache.creneau.fin.getMinutes() - (this.tache.creneau.debut.getHours() * 60 + this.tache.creneau.debut.getMinutes())) / (24 * 60)) * 100 + '%';
		this.element.style.width = `calc(${100 / this.total}% - 4px)`;
		this.element.style.margin = `0 2px`;
		this.element.style.left = `${(100 / this.total) * this.position}%`;
		this.element.style.transform = `translateX(0%)`;
		this.element.style.borderColor = `rgb(${this.tache.poste.toColor().join(',')})`;
		this.element.style.backgroundColor = `rgb(${this.tache.poste.toColor().join(',')}, 0.1)`;
		this.element.style.color = `rgb(${this.tache.poste.toColor().join(',')})`;
	}
}
