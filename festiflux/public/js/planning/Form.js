import { DateField, FormField, SelectField, TextField } from './FormField';

export class FormBuilder {
	constructor() {
		this.form = document.createElement('form');
		this.fields = [];
	}

	/**
	 * Ajoute un champ au formulaire.
	 * @param {FormField} field - Champ à ajouter.
	 */
	addField(field) {
		const fieldContainer = field.render();
		this.fields.push(field);
		this.form.appendChild(fieldContainer);
	}

	addSubmitButton(label) {
		const submitButton = document.createElement('button');
		submitButton.type = 'submit';
		submitButton.textContent = label;
		this.form.appendChild(submitButton);
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
			const isValid = field.validateField(inputElement.value);
			if (!isValid) return false;
		}
		return true;
	}

	/**
	 * Récupère les données du formulaire sous forme d'objet.
	 * @returns {Object} - Objet contenant les valeurs des champs.
	 */
	getFormData() {
		const formData = {};

		this.fields.forEach(field => {
			const inputElement = this.form.querySelector(`[name="${field.name}"]`);
			const value = field.type === 'checkbox' ? inputElement.checked : inputElement.value;
			formData[field.name] = value;

			// Valide le champ au moment de la soumission du formulaire
			field.validateField(value);
		});

		return formData;
	}
}

// Exemple d'utilisation
export const testForm = new FormBuilder();

testForm.addField(
	new TextField(
		'Nom',
		'nom',
		'Entrez votre nom',
		value => {
			if (value.length < 3) return 'Le nom doit contenir au moins 3 caractères.';
		},
		{ required: 'true' }
	)
);

testForm.addField(
	new DateField(
		'Date de naissance',
		'dateNaissance',
		'Entrez votre date de naissance',
		value => {
			if (new Date(value) > new Date()) return 'La date de naissance ne peut pas être dans le futur.';
		},
		{ required: 'true' }
	)
);

testForm.addField(
	new SelectField(
		'Sexe',
		'sexe',
		'Entrez votre sexe',
		value => {
			if (value === '') return 'Veuillez sélectionner une option.';
		},
		[
			{ label: 'Homme', value: 'homme' },
			{ label: 'Femme', value: 'femme' },
			{ label: 'Autre', value: 'autre' }
		],
		{ required: 'true' }
	)
);

testForm.addSubmitButton('Envoyer');
testForm.onSubmit(data => {
	console.log(data);
});
