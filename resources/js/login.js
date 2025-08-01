/// <reference path="../js/lib/jquery.js" />

const form = {};

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
                errorMessage.text(res["error"] ?? "Erro interno no servidor").addClass("show");
            },
        });
    });

    // Initial state
    checkComplete();
});
