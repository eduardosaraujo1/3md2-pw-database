/// <reference path="./lib/jquery.js" />

class FormController {
    /**
     * @param {string[]} inputIds - Array de IDs dos inputs do formulário
     */
    constructor(inputIds) {
        if (!Array.isArray(inputIds) || inputIds.length === 0) {
            throw new Error("inputIds must be a non-empty array");
        }
        this.inputIds = inputIds;
        this.state = {};
        this.inputs = inputIds.map((id) => $(`#${id}`));
        this.pullFromDOM();
    }

    clear() {
        this.inputIds.forEach((id) => {
            const field = $(`input#${id}`);
            this.state[id] = "";
            field.val("");
        });
    }

    pullFromDOM() {
        this.inputIds.forEach((id) => {
            const field = $(`input#${id}`);
            let value;

            if (field.attr("type") === "file") {
                value = field.prop("files")?.[0] || null;
            } else {
                value = field.val() ?? "";
            }

            this.state[id] = value;
        });
    }

    pushToDOM() {
        this.inputIds.forEach((id) => {
            const field = $(`input#${id}`);
            if (id === "foto") {
                // Se necessário, adicionar lógica para colocar a imagem de volta no input
            } else {
                field.val(this.state[id] ?? "");
            }
        });
    }

    getValues() {
        return { ...this.state };
    }

    setValue(id, value) {
        if (this.inputIds.includes(id)) {
            this.state[id] = value;
            const field = $(`input#${id}`);
            if (id === "foto") {
                // Se necessário, adicionar lógica para colocar a imagem de volta no input
            } else {
                field.val(value);
            }
        }
    }

    checkComplete() {
        // Considera campo de arquivo como preenchido se houver arquivo
        return this.inputIds.every((id) => {
            if (id === "foto") {
                const field = $(`input#${id}`);
                return !!field.prop("files")?.[0];
            }
            const field = $(`input#${id}`);
            return !!field.val();
        });
    }

    submit(endpoint) {
        const formData = new FormData();
        const values = this.getValues();

        for (const key in values) {
            formData.append(key, values[key]);
        }

        return sendFormData({
            endpoint,
            method: "POST",
            formData,
        });
    }
}

/**
 * A class for validating input values based on a set of rules.
 */
class Validator {
    /**
     * Creates an instance of Validator.
     * @param {Object<string, function>} rules - An object where each key is a field name and each value is a validation function.
     * The validation function takes two arguments: the value of the field and the entire set of values, and returns an error message
     * if the validation fails, or `null`/`undefined` if it passes.
     */
    constructor(rules) {
        this.rules = rules;
    }

    /**
     * Validates the provided values against the defined rules.
     * @param {Object<string, any>} values - An object containing field names as keys and their corresponding values to validate.
     * @returns {Object<string, string>} An object containing validation errors, where each key is a field name and the value is the error message.
     * If no errors are found, the object will be empty.
     */
    validate(values) {
        const errors = {};

        for (const [field, rule] of Object.entries(this.rules)) {
            const value = values[field];
            // value é o valor do input verificado agora, values é o valor de todos os inputs do FormController
            const error = rule(value, values);
            if (error) {
                errors[field] = error;
            }
        }

        return errors;
    }
}

/**
 * Sends form data to a specified endpoint using an AJAX request.
 *
 * @param {Object} params - The parameters for the request.
 * @param {string} params.endpoint - The URL endpoint to send the request to.
 * @param {string} [params.method="POST"] - The HTTP method to use for the request (default is "POST").
 * @param {FormData} params.formData - The FormData object containing the data to be sent.
 * @returns {Promise<Object>} A promise that resolves with the JSON response or rejects with the jQuery.jqXHR object.
 */
async function sendFormData({ endpoint, method = "POST", formData }) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: endpoint,
            method: method,
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                resolve(response); // Resolves with JSON response
            },
            /** @param {jQuery.jqXHR} xhr */
            error: function (xhr) {
                reject(xhr); // Rejects with jQuery.jqXHR object
            },
        });
    });
}
