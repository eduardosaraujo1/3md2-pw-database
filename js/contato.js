let errors = {};

function isValidPassword(password) {
    if (typeof password !== "string") return false;

    const minLength = 8;
    const hasUpperCase = /[A-Z]/;
    const hasLowerCase = /[a-z]/;
    const hasNumber = /\d/;
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>_\-\\\/[\];'`~+=]/;

    if (password.length < minLength) {
        errors["senha"] = "Senha sua senha deve conter no mínimo 8 caracteres";
        return false;
    }
    if (!hasUpperCase.test(password)) {
        errors["senha"] = "Senha deve conter pelo menos uma letra maiúscula";
        return false;
    }
    if (!hasNumber.test(password)) {
        errors["senha"] = "Senha deve conter pelo menos um número";
        return false;
    }
    if (!hasSpecialChar.test(password)) {
        errors["senha"] = "Senha deve conter pelo menos um caractere especial";
        return false;
    }
    if (!hasLowerCase.test(password)) {
        errors["senha"] = "Senha deve conter pelo menos uma letra minúscula";
        return false;
    }

    return true;
}

function isFormValid() {
    let valid = true;
    errors = {};

    const base = $("#mainForm")[0].checkValidity();
    if (!base) {
        valid = false;
    }

    const nome = $("#nome").val().trim();
    if (!nome) {
        errors["nome"] = "Esse campo é obrigatório";
        valid = false;
    }

    const login = $("#login").val().trim();
    if (!login) {
        errors["login"] = "Esse campo é obrigatório";
        valid = false;
    }

    const telefone = $("#telefone").val().trim();
    if (!telefone) {
        errors["telefone"] = "Esse campo é obrigatório";
        valid = false;
    }

    const emailValid = $("#email").val().includes("@");
    if (!emailValid) {
        errors["email"] = "Insira um e-mail válido";
        valid = false;
    }

    const passwordValid = isValidPassword($("#senha").val());
    if (!passwordValid) {
        valid = false;
    }

    const passwordEqual = $("#senha").val() === $("#confirm_senha").val();
    if (!passwordEqual) {
        errors["confirm_senha"] = "Senhas não coincidem";
        valid = false;
    }

    return valid;
}

function displayErrors(errs) {
    $(".field-error").hide();
    for (const field in errs) {
        if (Object.prototype.hasOwnProperty.call(errs, field)) {
            const errorVal = errs[field];
            $(`[data-field=${field}]`).show().html(errorVal);
        }
    }
}

function updateForm() {
    const valid = isFormValid();
    $("#submit").prop("disabled", !valid);
    displayErrors(errors);
}

$(() => {
    $("input").on("keyup", updateForm);
    // updateForm();

    $("form").on("submit", (e) => {
        e.preventDefault();
        const form = $(e.currentTarget)[0];
        const formData = new FormData(form);

        $.ajax({
            url: $(e.currentTarget).attr("action"),
            data: formData,
            method: $(e.currentTarget).attr("method"),
            processData: false,
            contentType: false,
            dataType: "html",
        })
            .done((content) => {
                $("#output").html(content);
            })
            .fail(() => {
                $("#output").html(`
            Ocorreu uma falha nessa requisição
        `);
            });
    });
});
