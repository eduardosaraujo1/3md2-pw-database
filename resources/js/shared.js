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

    submit(endpoint, onSuccess, onError) {
        const formData = new FormData();
        for (const [key, value] of Object.entries(this.getValues())) {
            formData.append(key, value);
        }
        sendFormData({
            endpoint,
            method: "POST",
            formData,
            onSuccess: (response) => {
                if (onSuccess) onSuccess(response);
            },
            /** @param {JQuery.jqXHR} xhr */
            onError: (xhr) => {
                if (onError) onError(xhr);
            },
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

function sendFormData({ endpoint, method = "POST", formData, onSuccess, onError }) {
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
            if (typeof onSuccess === "function") {
                onSuccess(response);
            }
        },
        error: function (xhr) {
            if (typeof onError === "function") {
                onError(xhr);
            } else {
                console.error("AJAX error:", xhr);
            }
        },
    });
}
