export class FormField {
	constructor(label, name, placeholder, validator, attributes = {}) {
		this.label = label;
		this.name = name;
		this.placeholder = placeholder;
		this.validator = validator;
		this.attributes = attributes;
	}

	render() {
		const fieldContainer = document.createElement('div');
		fieldContainer.classList.add('field', `field-${this.name}`);

		const errorDiv = document.createElement('div');
		errorDiv.className = 'error';
		fieldContainer.appendChild(errorDiv);

		const labelElement = document.createElement('label');
		labelElement.textContent = this.label;
		fieldContainer.appendChild(labelElement);

		const inputElement = this.createInputElement();
		inputElement.name = this.name;
		inputElement.placeholder = this.placeholder;

		// Ajout des attributs supplémentaires
		for (const [attrName, attrValue] of Object.entries(this.attributes)) {
			inputElement.setAttribute(attrName, attrValue);
		}

		// Ajout de l'événement de validation lors de la saisie
		inputElement.addEventListener('input', () => {
			this.validateField(inputElement.value);
		});

		fieldContainer.appendChild(inputElement);

		return fieldContainer;
	}

	createInputElement() {
		// Cette méthode doit être implémentée par les sous-classes
		throw new Error('createInputElement must be implemented by subclasses');
	}

	validateField(value) {
		const errorElement = document.querySelector(`.field-${this.name} .error`);
		const errorMessage = this.validator ? this.validator(value) : '';
		errorElement.textContent = errorMessage;
		return !errorMessage;
	}
}

export class TextField extends FormField {
	createInputElement() {
		const inputElement = document.createElement('input');
		inputElement.type = 'text';
		return inputElement;
	}
}

export class DateField extends FormField {
	constructor(label, name, placeholder, validator, attributes = {}) {
		super(label, name, placeholder, validator, attributes);
	}

	createInputElement() {
		const inputElement = document.createElement('input');
		inputElement.type = 'date';
		return inputElement;
	}
}

export class SelectField extends FormField {
	constructor(label, name, placeholder, validator, options = [], attributes = {}) {
		super(label, name, placeholder, validator, attributes);
		this.options = options;
	}

	createInputElement() {
		const inputElement = document.createElement('select');

		for (const option of this.options) {
			const optionElement = document.createElement('option');
			optionElement.value = option.value;
			optionElement.textContent = option.label;
			inputElement.appendChild(optionElement);
		}

		return inputElement;
	}
}
