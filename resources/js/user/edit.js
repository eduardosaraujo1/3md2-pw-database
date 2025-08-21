/// <reference path="./store.js" />

$(() => {
    // Declarar
    const formController = new FormController(
        $(".js-edit-form input")
            .map((_, el) => el.id)
            .get()
    );
    const formUI = new FormUserInterface($(".js-edit-form"));

    const validator = new Validator({
        "edit-nome": (value) => (!value?.trim() ? ERROR_DICTIONARY.empty_field : null),
        "edit-login": (value) => (!value?.trim() ? ERROR_DICTIONARY.empty_field : null),
        "edit-email": (value) => (!/^[^@]+@[^@]+$/gm.test(value ?? "") ? ERROR_DICTIONARY.email_invalid : null),
        "edit-telefone": (value) => (!/^\d{11,13}$/gm.test(value ?? "") ? ERROR_DICTIONARY.phone_invalid : null),
        "edit-senha": (value, values) => {
            if (!value) return ERROR_DICTIONARY.empty_field;
            if (value.length < 8) return ERROR_DICTIONARY.password_eight;
            if (!value.match(/[a-z]/g)) return ERROR_DICTIONARY.password_lower;
            if (!value.match(/[A-Z]/g)) return ERROR_DICTIONARY.password_upper;
            if (!value.match(/[^A-Za-z0-9]/g)) return ERROR_DICTIONARY.password_special;
            if (!value.match(/\d/g)) return ERROR_DICTIONARY.password_number;
            return null;
        },
        "edit-confirmSenha": (value, values) => {
            if (!value?.trim()) return ERROR_DICTIONARY.empty_field;
            if (value !== values["edit-senha"]) return ERROR_DICTIONARY.password_mismatch;
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
            formUI.displayErrors(errors);
            btnSubmit.attr("disabled", true);
        } else {
            formUI.clearErrors();
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
                formUI.displayGeneralMessage(msg?.["message"] ?? "Erro desconhecido");
            } else {
                throw err;
            }
        }
    });

    btnCancel.on("click", () => {
        formController.clear();
        refreshForm();
        formUI.displayGeneralMessage("");
    });

    window.onEditPress = (event) => {
        // Get the target element
        const userId = $(event.currentTarget).data("target");
        const userRow = $(`#userTable .js-id-field[data-target="${userId}"]`).closest("tr");
        formController.state = {
            "edit-id": userRow.find(".js-id-field").text(),
            "edit-nome": userRow.find(".js-nome-field").text(),
            "edit-login": userRow.find(".js-login-field").text(),
            "edit-email": userRow.find(".js-email-field").text(),
            "edit-telefone": userRow.find(".js-telefone-field").text(),
            "edit-senha": "",
            "edit-confirm-senha": "",
        };
        formController.pushToDOM();
        refreshForm();
    };

    window.fillFormEdit = function () {
        $("#edit-senha").val("Senha123!");
        $("#edit-confirmSenha").val("Senha123!");

        refreshForm();
    };
});
