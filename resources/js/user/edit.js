/// <reference path="./store.js" />

$(() => {
    // Declarar
    const formController = new FormController(
        $(".js-edit-form input")
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

    const btnSubmit = $(".js-edit-form button#edit-submit");
    const btnCancel = $(".js-edit-form button#edit-cancel");

    // Lógica
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

    $(".js-edit-form input").on("keyup change", refreshForm);
    refreshForm();

    btnSubmit.on("click", async () => {
        try {
            await formController.submit("/users/update");

            // on success
            $(document).trigger("reloadUserTable");
            btnCancel.trigger("click");
        } catch (err) {
            if (err && typeof err === "object" && "responseJSON" in err) {
                const msg = err.responseJSON;
                FormUserInterface.displayGeneralMessage(msg?.["message"] ?? "Erro desconhecido");
            } else {
                throw err;
            }
        }
    });

    btnCancel.on("click", () => {
        formController.clear();
        refreshForm();
        FormUserInterface.displayGeneralMessage("");
    });

    $(".js-btn-edit").on("click", () => {
        // Code to be written later
        // TODO: Error display broken, validation unlock broken, autofill incomplete
    });

    window.fillForm = function () {
        $("#nome").val("Teste");
        $("#login").val("teste" + Math.floor(Math.random() * 1000));
        $("#email").val("teste" + Math.floor(Math.random() * 1000) + "@teste.com");
        $("#telefone").val("11987654321");
        $("#senha").val("Senha123!");
        $("#confirmSenha").val("Senha123!");

        refreshForm();
    };
});
