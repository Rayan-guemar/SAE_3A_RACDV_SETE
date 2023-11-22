import { modalManager } from './ModalManager.js';

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
		fieldContainer.classList.add('field', 'field-' + name);

		const errorDiv = document.createElement('div');
		errorDiv.className = 'error';
		fieldContainer.appendChild(errorDiv);

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

		this.fields.push({ name, type: 'text' });
		this.validators[name] = validator; // Stockage de la fonction de validation

		this.form.appendChild(fieldContainer);
	}

	addDateField(label, name, placeholder, validator, min, max, value = '', required = false) {
		const fieldContainer = document.createElement('div');
		fieldContainer.classList.add('field', 'field-' + name);

		const errorDiv = document.createElement('div');
		errorDiv.className = 'error';
		fieldContainer.appendChild(errorDiv);

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
		inputElement.required = required;

		// Ajout de l'événement de validation lors de la saisie
		inputElement.addEventListener('input', () => {
			this.validateField(name, inputElement.value, validator);
		});

		fieldContainer.appendChild(inputElement);

		this.fields.push({ name, type: 'date' });
		this.validators[name] = validator; // Stockage de la fonction de validation

		this.form.appendChild(fieldContainer);
	}
	/**
	 *
	 * @param {string} label
	 * @param {string} name
	 * @param {string} placeholder
	 * @param {(t:any) => string} validator
	 * @param {({value: string, label: string})[]} options
	 */
	addSelectField(label, name, placeholder, validator, options = []) {
		const fieldContainer = document.createElement('div');
		fieldContainer.classList.add('field', 'field-' + name);

		const errorDiv = document.createElement('div');
		errorDiv.className = 'error';
		fieldContainer.appendChild(errorDiv);

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

	addSubmitButton(label) {
		const submitButton = document.createElement('button');
		submitButton.type = 'submit';
		submitButton.textContent = label;
		this.form.appendChild(submitButton);
	}

	/**
	 * Valide un champ spécifique.
	 * @param {string} name - Nom du champ.
	 * @param {string} value - Valeur du champ.
	 * @param {Function} validator - Fonction de validation pour le champ.
	 */
	validateField(name, value) {
		const errorElement = this.form.querySelector(`.field-${name} .error`);
		const validator = this.validators[name];
		if (validator) {
			const errorMessage = validator(value);
			errorElement.textContent = errorMessage || '';
			return !errorMessage;
		}
		return true;
	}

	getForm() {
		return this.form;
	}

	onSubmit(callback) {
		this.form.addEventListener('submit', event => {
			event.preventDefault();
			if (this.isValid()) {
				callback(this.getFormData());
			}
		});
	}

	isValid() {
		for (const field of this.fields) {
			const inputElement = this.form.querySelector(`[name="${field.name}"]`);
			const isValid = this.validateField(field.name, inputElement.value, this.validators[field.name]);
			if (!isValid) return false;
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

export const testForm = new FormBuilder();
testForm.addTextField('Nom', 'nom', 'Entrez votre nom', value => {
	if (value.length < 3) return 'Le nom doit contenir au moins 3 caractères.';
});

testForm.addTextField('Prénom', 'prenom', 'Entrez votre prénom', value => {
	if (value.length < 3) return 'Le prénom doit contenir au moins 3 caractères.';
});

testForm.addDateField(
	'Date de naissance',
	'dateNaissance',
	'Entrez votre date de naissance',
	value => {
		if (new Date(value) > new Date()) return 'La date de naissance ne peut pas être dans le futur.';
	},
	'1900-01-01',
	new Date(),
	null,
	true
);

testForm.addSelectField(
	'Sexe',
	'sexe',
	'Entrez votre sexe',
	value => {
		if (value === '') return 'Veuillez sélectionner une option.';
	},
	[
		{
			label: 'Homme',
			value: 'homme'
		},
		{
			label: 'Femme',
			value: 'femme'
		},
		{
			label: 'Autre',
			value: 'autre'
		}
	]
);

testForm.addSubmitButton('Envoyer');
testForm.onSubmit(data => {
	console.log(data);
});
