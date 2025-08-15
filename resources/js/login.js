/// <reference path="./lib/jquery.js" />
/// <reference path="./shared.js" />

class FormController {
    constructor(inputIds) {
        if (inputIds == null) {
            throw new Error("inputIds cannot be null");
        }
        this.state = {};
        /** @type {JQuery[]} */
        this.inputs = inputIds.map((id) => $(`#${id}`));
    }

    pullFromDOM() {
        this.inputs.forEach((input) => {
            const name = input.attr("name");
            const value = input.val();
            this.state[name] = value;
        });
    }

    pushToDOM() {
        this.inputs.forEach((input) => {
            const name = input.attr("name");
            if (this.state[name] !== undefined) {
                input.val(this.state[name]);
            }
        });
    }

    checkComplete() {
        return this.inputs.every((input) => input.val());
    }

    submit(endpoint, onSuccess, onError) {
        const formData = new FormData();
        for (const key in this.state) {
            formData.append(key, this.state[key]);
        }
        sendFormData({
            endpoint,
            method: "POST",
            formData,
            onSuccess,
            /** @param {JQuery.jqXHR} xhr */
            onError,
        });
    }
}

$(() => {
    const formController = new FormController(["login", "senha"]); // Replace with actual input IDs

    const submitButton = $("#submitButton");
    const errorMessage = $("#errorMessage");

    function updateButtonState() {
        const isComplete = formController.checkComplete();
        submitButton.prop("disabled", !isComplete);
    }

    formController.inputs.forEach((input) => {
        input.on("keydown", () => {
            formController.pullFromDOM();
            updateButtonState();
        });
    });
    updateButtonState();

    submitButton.on("click", () => {
        formController.submit(
            "/signin",
            (res) => {
                if (res.error) {
                    errorMessage.text(res.error).addClass("show");
                } else if (res.status === "success") {
                    window.location.href = "/";
                }
            },
            (xhr) => {
                const res = xhr.responseJSON;
                errorMessage.text(res?.["error"] ?? "Erro interno no servidor").addClass("show");
            }
        );
    });
});
