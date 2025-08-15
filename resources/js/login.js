/// <reference path="./lib/jquery.js" />
/// <reference path="./shared.js" />

const form = {};

$(() => {
    const inputs = $("input");
    const submitButton = $("#submitButton");
    const errorMessage = $("#errorMessage");

    function readForm() {
        inputs.each(function () {
            const name = $(this).attr("name");
            const value = $(this).val();
            form[name] = value;
        });
    }

    function checkComplete() {
        let allFilled = true;

        inputs.each(function () {
            if (!$(this).val()) {
                allFilled = false;
            }
        });

        submitButton.prop("disabled", !allFilled);
    }

    // Attach keydown and input listeners
    inputs.on("keydown", function () {
        readForm();
        checkComplete();
    });

    // Submit button click handler
    submitButton.on("click", function () {
        const formData = new FormData();

        for (const key in form) {
            formData.append(key, form[key]);
        }

        sendFormData({
            endpoint: "/signin",
            method: "POST",
            formData,
            onSuccess: function (res) {
                if (res.error) {
                    errorMessage.text(res.error).addClass("show");
                } else if (res.status === "success") {
                    window.location.href = "/";
                }
            },
            /** @param {JQuery.jqXHR} xhr */
            onError: function (xhr) {
                const res = xhr.responseJSON;
                errorMessage.text(res?.["error"] ?? "Erro interno no servidor").addClass("show");
            },
        });
    });

    // Initial state
    checkComplete();
});
