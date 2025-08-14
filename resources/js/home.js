/// <reference path="./lib/jquery.js" />
/// <reference path="./shared.js" />

let form = {};

$(() => {
    const inputs = $("input");
    const editButton = $("#editButton");
    const errorMessage = $("#errorMessage");

    let isEditing = false;

    function pushFormToDOM() {
        for (const key in form) {
            $(`#${key}`).val(form[key]);
        }
    }

    function readDOMToForm() {
        inputs.each(function () {
            const name = $(this).attr("name");
            form[name] = $(this).val();
        });
    }

    function toggleForm(enabled) {
        inputs.prop("disabled", !enabled);
    }

    // Fetch profile on load
    sendFormData({
        endpoint: "/profile",
        method: "POST",
        formData: new FormData(),
        onSuccess: function (res) {
            form = res;
            pushFormToDOM();
        },
        onError: function () {
            errorMessage.text("Erro ao carregar perfil").addClass("show");
        },
    });

    editButton.on("click", function () {
        if (!isEditing) {
            // Enter editing mode
            toggleForm(true);
            isEditing = true;
            $(this).text("Salvar");
        } else {
            // Save mode
            readDOMToForm();
            const formData = new FormData();
            for (const key in form) {
                formData.append(key, form[key]);
            }

            sendFormData({
                endpoint: "/profile/update",
                method: "POST",
                formData: formData,
                onSuccess: function (res) {
                    if (res.error) {
                        errorMessage.text(res.error).addClass("show");
                    } else {
                        errorMessage.text("").removeClass("show");
                        toggleForm(false);
                        isEditing = false;
                        editButton.text("Editar");
                    }
                },
                /** @param {JQuery.jqXHR} xhr */
                onError: function (xhr) {
                    const data = xhr.responseJSON;
                    errorMessage.text(data["error"] ?? "Erro ao salvar alterações").addClass("show");
                },
            });
        }
    });

    // Keep form updated live
    inputs.on("input", function () {
        if (isEditing) {
            const name = $(this).attr("name");
            form[name] = $(this).val();
        }
    });
});
