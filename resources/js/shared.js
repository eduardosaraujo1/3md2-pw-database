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
        this.inputIds.forEach((id, index) => {
            const field = $(`input#${id}`);
            // Suporte para campo de arquivo
            this.state[id] = id === "foto" ? field.prop("files")?.[0] : field.val() ?? "";
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

class Validator {
    constructor(rules) {
        this.rules = rules;
    }

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

async function sendFormData({ endpoint, method = "POST", formData }) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: endpoint,
            method: method,
            data: formData,
            contentType: false,
            processData: false,
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                resolve(response);
            },
            /** @param {jQuery.jqXHR} xhr */
            error: function (xhr) {
                reject(xhr);
            },
        });
    });
}
