/**
 * Form Handler
 *
 * @package Bonaire
 * @since 1.0.0
 */

export class FormHandler {
    constructor($, options) {
        this.$ = $;
        this.options = options;
    }

    getFormData(formSelector) {
        const $ = this.$;
        const $form = $(formSelector);

        if (!$form.length) {
            return null;
        }

        const formData = {};
        const serialized = $form.serializeArray();

        serialized.forEach(function (field) {
            // Handle nested option names like bonaire_options[username]
            const match = field.name.match(/bonaire_options\[(.*?)\]/);
            if (match) {
                formData[match[1]] = field.value;
            } else {
                formData[field.name] = field.value;
            }
        });

        return formData;
    }

    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    validateRequired(fields) {
        const $ = this.$;
        let isValid = true;

        Object.keys(fields).forEach(function (key) {
            const $field = $('#' + key);

            if (!fields[key] || fields[key].trim() === '') {
                $field.addClass('error');
                isValid = false;
            } else {
                $field.removeClass('error');
            }
        });

        return isValid;
    }

    sanitizeInput(input) {
        const temp = document.createElement('div');
        temp.textContent = input;
        return temp.innerHTML;
    }
}
