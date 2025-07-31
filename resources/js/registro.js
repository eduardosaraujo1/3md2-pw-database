/// <reference path="../js/lib/jquery.js" />
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
    password_missmatch: "As senhas não coincidem",
};

/**
 * @param {FormState} form
 * @param {string} field_id
 */
function syncForm(form, field_id = null) {
    const updateField = (key) => {
        const field = $(`input#${key}`);
        if (key === "foto") {
            form[key] = field.prop("files")?.[0];
        } else {
            form[key] = field.val() ?? "";
        }
    };

    if (field_id) {
        updateField(field_id);
    } else {
        // update entire form
        for (const key in form) {
            updateField(key);
        }
    }

    return form;
}

/**
 * @param {FormState} form
 */
function validateForm(form) {
    const errors = {};

    // null check
    const required = ["nome", "login", "email", "telefone", "senha"];
    for (const field of required) {
        const element = form[field];

        if (!element?.trim()) {
            errors[field] = ERROR_DICTIONARY.empty_field;
        }
    }

    // email validation
    if (!/^[^@]+@[^@]+$/gm.test(form.email ?? "")) {
        errors["email"] = ERROR_DICTIONARY.email_invalid;
    }

    // email validation
    if (!/^\d{11,13}$/gm.test(form.telefone ?? "")) {
        errors["telefone"] = ERROR_DICTIONARY.phone_invalid;
    }

    // password validation
    if (form.senha && form.senha.length < 8) {
        errors["senha"] = ERROR_DICTIONARY.password_eight;
    } else if (form.senha && !form.senha.match(/[a-z]/g)) {
        errors["senha"] = ERROR_DICTIONARY.password_lower;
    } else if (form.senha && !form.senha.match(/[A-Z]/g)) {
        errors["senha"] = ERROR_DICTIONARY.password_upper;
    } else if (form.senha && !form.senha.match(/[^A-Za-z0-9]/g)) {
        errors["senha"] = ERROR_DICTIONARY.password_special;
    } else if (form.senha && !form.senha.match(/\d/g)) {
        errors["senha"] = ERROR_DICTIONARY.password_number;
    } else if (form.senha && form.senha != form.confirmSenha) {
        errors["confirmSenha"] = ERROR_DICTIONARY.password_missmatch;
    }

    return errors;
}

function showErrors(errors) {
    clearErrors();

    for (const field in errors) {
        $(`[data-field=${field}]`).show().html(errors[field]);
    }
}

function clearErrors() {
    $(".field-error").hide();
}

$(() => {
    let meta = {
        action: "/signup",
        method: "post",
    };
    /** @type {FormState} */
    let form = {
        nome: "",
        login: "",
        senha: "",
        confirmSenha: "",
        email: "",
        telefone: "",
        foto: null,
    };

    const refreshForm = (field_id = null) => {
        let errors;
        form = syncForm(form, field_id);
        errors = validateForm(form);

        if (Object.keys(errors).length > 0) {
            showErrors(errors);
            $("button#submit").attr("disabled", true);
        } else {
            clearErrors();
            $("button#submit").attr("disabled", false);
        }
    };

    $("input").on("keyup change", (e) => {
        refreshForm(e.currentTarget.id ?? null);
    });

    $(".show-password").on("click", (e) => {
        const element = $(e.currentTarget);
        const target_id = element.data("target");
        const target = $(`#${target_id}`);
        const visible = element.data("visible");

        if (visible) {
            // make password invisible
            element.attr("src", "/resources/assets/icons/eye-slash-solid.svg");
            target.attr("type", "password");
        } else {
            // make password visible
            element.attr("src", "/resources/assets/icons/eye-solid.svg");
            target.attr("type", "text");
        }

        element.data("visible", !visible);
    });

    $("button#submit").on("click", () => {
        const formData = new FormData();
        for (const field in form) {
            formData.append(field, form[field]);
        }

        $.ajax({
            url: meta.action,
            data: formData,
            method: meta.method,
            processData: false, // por padrão, o Jquery transforma o FormData em queryString, isso previne isso
            contentType: false, // por padrão, o Jquery envia o tipo "texto", o que não suporta upload de arquivos
            dataType: "json",
        })
            .done((content) => {
                // $("#output").html(JSON.stringify(content, null, 2));
                console.log(content);
            })
            .fail((jqXHR) => {
                const err = jqXHR.responseJSON;
                // $("#output").html(JSON.stringify(err, null, 2));
                console.log(err);
            });
    });

    refreshForm();
});
