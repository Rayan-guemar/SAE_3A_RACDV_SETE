/**
 * Représente un objet Poste.
 * @class
 */
export class Poste {
    /**
     * Crée un nouvel objet Poste.
     * @constructor
     * @param {number} id - L'ID du poste.
     * @param {string} nom - Le nom du poste.
     */
    constructor(id, nom) {
        this.id = id;
        this.nom = nom;
    }

    static new(nom) {
        return new Poste(null, nom);
    }

    htmlNode() {
        let div = document.createElement('div');
        div.classList.add('poste');
        div.dataset.id = this.id;
        div.innerHTML = `
            <div class="poste-nom">${ this.nom }</div>
        `;
        return div;
    }

    html() {
        let html = `
            <div class="poste" data-id="${ this.id }">
                <div class="poste-nom">${ this.nom }</div>
            </div>
        `;

        return html;
    }
}