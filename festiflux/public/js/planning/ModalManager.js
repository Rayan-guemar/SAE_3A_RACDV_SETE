/**
 * Classe pour gérer l'affichage de modales sur le site.
 *
 * @class
 */
class ModalManager {
	/**
	 * Constructeur de la classe ModalManager.
	 * Initialise les éléments nécessaires pour la modal.
	 *
	 * @constructor
	 */
	constructor() {
		/**
		 * Élément de la modal.
		 * @type {HTMLElement}
		 */
		this.modal = null;

		/**
		 * Élément overlay pour assombrir le reste de la page.
		 * @type {HTMLElement}
		 */
		this.overlay = null;

		/**
		 * Élément de contenu de la modal.
		 * @type {HTMLElement}
		 */
		this.modalContent = null;

		/**
		 * Élément de l'en-tête de la modal.
		 * @type {HTMLElement}
		 */
		this.modalHeader = null;

		/**
		 * Titre de la modal.
		 * @type {HTMLElement}
		 */
		this.modalTitle = null;

		/**
		 * Bouton de fermeture de la modal.
		 * @type {HTMLButtonElement}
		 */
		this.closeButton = null;

		/**
		 * Contenu de la modal.
		 * @type {HTMLElement}
		 */
		this.content = null;

		this.createModalElement();
		this.createOverlay();

		// Attacher les gestionnaires d'événements
		document.body.appendChild(this.modal); // Ajouter la modale au corps du document

		this.closeButton.addEventListener('click', this.close.bind(this));
	}

	/**
	 * Crée l'élément overlay de la modal.
	 *
	 * @private
	 */
	createOverlay() {
		/**
		 * Overlay de la modal.
		 * @type {HTMLElement}
		 */
		const overlay = document.createElement('div');
		overlay.className = 'overlay';
		this.overlay = overlay;
		this.modal.appendChild(overlay);
	}

	/**
	 * Crée les éléments de la modal.
	 *
	 * @private
	 */
	createModalElement() {
		this.modal = document.createElement('div');
		this.modal.className = 'modal';

		this.modalContent = document.createElement('div');
		this.modalContent.className = 'modal-content';

		this.modalHeader = document.createElement('div');
		this.modalHeader.className = 'modal-header';

		this.modalTitle = document.createElement('h2');
		this.modalTitle.textContent = '';

		this.closeButton = document.createElement('button');
		this.closeButton.className = 'modal-close-button';
		this.closeButton.textContent = 'X';

		this.modalHeader.appendChild(this.modalTitle);
		this.modalHeader.appendChild(this.closeButton);

		this.modal.appendChild(this.modalHeader);

		this.modal.appendChild(this.modalContent);
	}

	/**
	 * Ouvre la modal avec un titre et un contenu spécifiés.
	 *
	 * @param {string} title - Le titre de la modal.
	 * @param {HTMLElement} content - Le contenu de la modal.
	 */
	openWith(title, content) {
		this.close();
		this.modalTitle.textContent = title;
		this.content = content;
		this.modal.appendChild(content);
		this.modal.style.display = 'block';
		document.body.style.overflow = 'hidden'; // Empêcher le défilement de la page lorsque la modale est ouverte
	}

	/**
	 * Ferme la modal.
	 */
	close() {
		this.modal.style.display = 'none';
		document.body.style.overflow = 'auto'; // Rétablir le défilement de la page lorsque la modale est fermée
		this.modalContent.innerHTML = '';
		this.modalTitle.textContent = '';
		this.content = null;
	}
}

/**
 * Instance unique de la classe ModalManager pour gérer les modales sur le site.
 * @type {ModalManager}
 */
export const modalManager = new ModalManager();
