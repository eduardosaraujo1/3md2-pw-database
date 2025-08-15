/// <reference path="../shared.js" />
/// <reference path="../lib/jquery.js" />
/**
 * @typedef {Object} FormState
 * @property {string} nome
 * @property {string} login
 * @property {string} senha
 * @property {string} confirmSenha
 * @property {string} email
 * @property {string} telefone
 * @property {?File} foto
 */

const ERROR_DICTIONARY = {
    empty_field: "Este campo não pode ser vazio",
    email_invalid: "Por favor digite um e-mail válido",
    phone_invalid: "Por favor digite um telefone válido",
    password_eight: "A senha deve conter no mínimo 8 caracteres",
    password_special: "A senha deve conter no mínimo um caractere especial",
    password_number: "A senha deve conter no mínimo um número",
    password_upper: "A senha deve conter no mínimo uma letra maiúscula",
    password_lower: "A senha deve conter no mínimo uma letra minúscula",
    password_mismatch: "As senhas não coincidem",
};

class FormUserInterface {
    static displayErrors(errors) {
        FormUserInterface.clearErrors();

        for (const [field, message] of Object.entries(errors)) {
            const input = $(`#${field}`);
            input.addClass("has-error");
            input.attr("title", message);
        }
    }

    static clearErrors() {
        $(".has-error").removeClass("has-error").removeAttr("title");
    }

    static displayGeneralMessage(message) {
        const output = $(".js-register-form .js-form-message");
        output.html(message);
    }
}

$(() => {
    // Declarar
    const formController = new FormController(
        $(".js-register-form input")
            .map((_, el) => el.id)
            .get()
    );

    const validator = new Validator({
        nome: (value) => (!value?.trim() ? ERROR_DICTIONARY.empty_field : null),
        login: (value) => (!value?.trim() ? ERROR_DICTIONARY.empty_field : null),
        email: (value) => (!/^[^@]+@[^@]+$/gm.test(value ?? "") ? ERROR_DICTIONARY.email_invalid : null),
        telefone: (value) => (!/^\d{11,13}$/gm.test(value ?? "") ? ERROR_DICTIONARY.phone_invalid : null),
        senha: (value, values) => {
            if (!value) return ERROR_DICTIONARY.empty_field;
            if (value.length < 8) return ERROR_DICTIONARY.password_eight;
            if (!value.match(/[a-z]/g)) return ERROR_DICTIONARY.password_lower;
            if (!value.match(/[A-Z]/g)) return ERROR_DICTIONARY.password_upper;
            if (!value.match(/[^A-Za-z0-9]/g)) return ERROR_DICTIONARY.password_special;
            if (!value.match(/\d/g)) return ERROR_DICTIONARY.password_number;
            return null;
        },
        confirmSenha: (value, values) => {
            if (!value?.trim()) return ERROR_DICTIONARY.empty_field;
            if (value !== values.senha) return ERROR_DICTIONARY.password_mismatch;
            return null;
        },
    });

    const btnSubmit = $(".js-register-form button#register-submit");
    const btnCancel = $(".js-register-form button#register-cancel");

    // Lógica
    $(".show-password").on("click", (e) => {
        const element = $(e.currentTarget);
        const targetId = element.data("target");
        const target = $(`#${targetId}`);
        const visible = element.data("visible");

        if (visible) {
            element.attr("src", "/resources/assets/icons/eye-slash-solid.svg");
            target.attr("type", "password");
        } else {
            element.attr("src", "/resources/assets/icons/eye-solid.svg");
            target.attr("type", "text");
        }

        element.data("visible", !visible);
    });

    const refreshForm = () => {
        formController.pullFromDOM();
        const values = formController.getValues();
        const errors = validator.validate(values);

        if (Object.keys(errors).length > 0) {
            FormUserInterface.displayErrors(errors);
            btnSubmit.attr("disabled", true);
        } else {
            FormUserInterface.clearErrors();
            btnSubmit.attr("disabled", false);
        }
    };

    $(".js-register-form input").on("keyup change", refreshForm);
    refreshForm();

    btnSubmit.on("click", () => {
        formController.submit(
            "/users/store",
            (response) => {
                btnCancel.trigger("click");
            },
            /** @param {JQuery.jqXHR} xhr */
            (xhr) => {
                const err = xhr.responseJSON;
                FormUserInterface.displayGeneralMessage(err?.error ?? "Ocorreu um erro desconhecido", "error");
            }
        );
    });
    btnCancel.on("click", () => {
        formController.clear();
        refreshForm();
    });
});

// Testes
function fillForm() {
    document.querySelector("#nome").value = "Teste";
    document.querySelector("#login").value = "teste" + Math.floor(Math.random() * 1000);
    document.querySelector("#email").value = "teste" + Math.floor(Math.random() * 1000) + "@teste.com";
    document.querySelector("#telefone").value = "11987654321";
    document.querySelector("#senha").value = "Senha123!";
    document.querySelector("#confirmSenha").value = "Senha123!";
}
