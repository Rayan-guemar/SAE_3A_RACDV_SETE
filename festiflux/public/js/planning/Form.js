class FormBuilder {
	constructor() {
		this.form = document.createElement('form');
		this.fields = [];
		this.validators = {}; // Ajout de la propriété validators
	}

	/**
	 * Ajoute un champ au formulaire.
	 * @param {string} label - Libellé du champ.
	 * @param {string} type - Type du champ (text, email, etc.).
	 * @param {string} name - Nom du champ (utilisé comme attribut "name" dans le formulaire).
	 * @param {string} placeholder - Placeholder du champ.
	 * @param {Function} validator - Fonction de validation pour le champ.
	 */
	addTextField(label, name, placeholder, validator) {
		const fieldContainer = document.createElement('div');

		const labelElement = document.createElement('label');
		labelElement.textContent = label;
		fieldContainer.appendChild(labelElement);

		const inputElement = document.createElement('input');
		inputElement.type = 'text';
		inputElement.name = name;
		inputElement.placeholder = placeholder;

		// Ajout de l'événement de validation lors de la saisie
		inputElement.addEventListener('input', () => {
			this.validateField(name, inputElement.value, validator);
		});

		fieldContainer.appendChild(inputElement);

		this.fields.push({ name, type });
		this.validators[name] = validator; // Stockage de la fonction de validation

		this.form.appendChild(fieldContainer);
	}

	addDateField(label, name, placeholder, validator, min, max, value = '') {
		const fieldContainer = document.createElement('div');

		const labelElement = document.createElement('label');
		labelElement.textContent = label;
		fieldContainer.appendChild(labelElement);

		const inputElement = document.createElement('input');
		inputElement.type = 'date';
		inputElement.name = name;
		inputElement.placeholder = placeholder;
		inputElement.min = new Date(min).toISOString().split('T')[0];
		inputElement.max = new Date(max).toISOString().split('T')[0];
		inputElement.value = value;

		// Ajout de l'événement de validation lors de la saisie
		inputElement.addEventListener('input', () => {
			this.validateField(name, inputElement.value, validator);
		});

		fieldContainer.appendChild(inputElement);

		this.fields.push({ name, type: 'date' });
		this.validators[name] = validator; // Stockage de la fonction de validation

		this.form.appendChild(fieldContainer);
	}

	addSelectField(label, name, placeholder, validator, options) {
		const fieldContainer = document.createElement('div');

		const labelElement = document.createElement('label');
		labelElement.textContent = label;
		fieldContainer.appendChild(labelElement);

		const inputElement = document.createElement('select');
		inputElement.name = name;
		inputElement.placeholder = placeholder;

		// Ajout de l'événement de validation lors de la saisie
		inputElement.addEventListener('input', () => {
			this.validateField(name, inputElement.value, validator);
		});

		for (const option of options) {
			const optionElement = document.createElement('option');
			optionElement.value = option.value;
			optionElement.textContent = option.label;
			inputElement.appendChild(optionElement);
		}

		fieldContainer.appendChild(inputElement);

		this.fields.push({ name, type: 'select' });
		this.validators[name] = validator; // Stockage de la fonction de validation

		this.form.appendChild(fieldContainer);
	}

	/**
	 * Valide un champ spécifique.
	 * @param {string} name - Nom du champ.
	 * @param {string} value - Valeur du champ.
	 * @param {Function} validator - Fonction de validation pour le champ.
	 */
	validateField(name, value, validator) {
		const errorElement = this.form.querySelector(`[data-error="${name}"]`);
		if (validator) {
			const errorMessage = validator(value);
			errorElement.textContent = errorMessage || '';
		}
	}

	/**
	 * Récupère les données du formulaire sous forme d'objet.
	 * @returns {Object} - Objet contenant les valeurs des champs.
	 */
	getFormData() {
		const formData = {};

		this.fields.forEach(({ name, type }) => {
			const inputElement = this.form.querySelector(`[name="${name}"]`);
			const value = type === 'checkbox' ? inputElement.checked : inputElement.value;
			formData[name] = value;

			// Valide le champ au moment de la soumission du formulaire
			this.validateField(name, value, this.validators[name]);
		});

		return formData;
	}
}

export class CInput {
	constructor(label, type, name = '', placeholder = '', validator = null) {
		this.label = label;
		this.type = type;
		this.name = name;
		this.placeholder = placeholder;
		this.validator = validator;

		this.create();
	}

	create() {
		this.fieldContainer = document.createElement('div');

		this.labelElement = document.createElement('label');
		this.labelElement.textContent = this.label;
		this.fieldContainer.appendChild(this.labelElement);

		this.inputElement = document.createElement('input');
		this.inputElement.type = this.type;
		this.inputElement.name = this.name;
		this.inputElement.placeholder = this.placeholder;

		this.fieldContainer.appendChild(this.inputElement);
	}
}

export class CInput_Text extends CInput {
	constructor(label, name, placeholder, validator) {
		super(label, 'text', name, placeholder, validator);
	}
}

export class CInput_Date extends CInput {
	constructor(label, name, placeholder, validator, min, max) {
		super(label, 'date', name, placeholder, validator);
		this.inputElement.min = new Date(min).toISOString().split('T')[0];
		this.inputElement.max = new Date(max).toISOString().split('T')[0];
	}
}
