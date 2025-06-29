let errors = [];
function isValidPassword(password) {
    if (typeof password !== "string") return false;
    let valid = true;

    const minLength = 8;
    const hasUpperCase = /[A-Z]/;
    const hasLowerCase = /[a-z]/;
    const hasNumber = /\d/;
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>_\-\\\/[\];'`~+=]/;

    if (password.length < minLength) {
        errors.push("Senha: sua senha deve conter no mínimo 8 caracteres");
        valid = false;
    }
    if (!hasUpperCase.test(password)) {
        errors.push("Senha: deve conter pelo menos uma letra maiúscula");
        valid = false;
    }
    if (!hasNumber.test(password)) {
        errors.push("Senha: deve conter pelo menos um número");
        valid = false;
    }
    if (!hasSpecialChar.test(password)) {
        errors.push("Senha: deve conter pelo menos um caractere especial");
        valid = false;
    }
    if (!hasLowerCase.test(password)) {
        errors.push("Senha: deve conter pelo menos uma letra minúscula");
        valid = false;
    }

    return valid;
}

function isFormValid() {
    errors = [];
    let valid = true;

    const base = $("#mainForm")[0].checkValidity();
    if (!base) {
        errors.push("Geral: Por favor preencha os campos obrigatórios.");
        valid = false;
    }

    const emailValid = $("#email").val().includes("@");
    if (!emailValid) {
        errors.push("E-mail: Insira um e-mail válido");
        valid = false;
    }

    const passwordValid = isValidPassword($("#senha").val());
    if (!passwordValid) {
        valid = false;
    }

    const passwordEqual = $("#senha").val() === $("#confirm_senha").val();
    if (!passwordEqual) {
        errors.push("Confirmar Senha: senhas não coincidem");
        valid = false;
    }

    return valid;
}

function updateForm() {
    const valid = isFormValid();
    $("#submit").prop("disabled", !valid);

    const container = $("#errorContainer");
    container.empty();

    for (const err of errors) {
        container.append(`<li>${err}</li>`);
    }

    const show_on_error = $(".js-show-on-error");
    if (errors.length === 0) {
        show_on_error.hide();
    } else {
        show_on_error.show();
    }
}

$(() => {
    $("input").on("keyup", updateForm);
    updateForm();

    $("form").on("submit", (e) => {
        e.preventDefault();

        const form = $(e.currentTarget);
        const data = form.serialize();

        $.ajax({
            url: form.attr("action"),
            data: data,
            method: form.attr("method"),
            dataType: "html",
        })
            .done((content) => {
                $("#output").html(content);
            })
            .fail((content) => {
                $("#output").html(`
              Ocorreu uma falha nessa requisição
            `);
            });
    });
});
