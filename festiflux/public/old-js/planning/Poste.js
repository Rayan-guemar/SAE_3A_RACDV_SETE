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
     * @param {string} couleur - La couleur du poste
     */
    constructor(id, nom, couleur) {
        this.id = id;
        this.nom = nom;
        this.couleur = couleur;
    }

    static new(nom, couleur) {
        return new Poste(null, nom, couleur);
    }

    toColor() {
        let colors = [
            [97, 26, 221],
            [255, 68, 84],
        ];

        let nameHash = this.nom.hashCode();

        let mod = nameHash % colors.length;
        if (mod < 0) {
            mod = colors.length + mod;
        }
        let color = colors[mod];

        return color;
    }

    toColor() {
        let colors = [
            [97, 26, 221],
            [255, 68, 84],
        ];

        let nameHash = this.nom.hashCode();

        let mod = nameHash % colors.length;
        if (mod < 0) {
            mod = colors.length + mod;
        }
        let color = colors[mod];

        return color;
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