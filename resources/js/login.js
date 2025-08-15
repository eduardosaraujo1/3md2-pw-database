/// <reference path="./lib/jquery.js" />
/// <reference path="./shared.js" />

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
        (async () => {
            try {
                const res = await formController.submit("/signin");
                if (res.error) {
                    errorMessage.text(res.error).addClass("show");
                } else if (res.status === "success") {
                    window.location.href = "/";
                }
            } catch (err) {
                if (err && typeof err === "object" && "responseJSON" in err) {
                    const res = err.responseJSON;
                    errorMessage.text(res?.["error"] ?? "Erro interno no servidor").addClass("show");
                } else {
                    throw err;
                }
            }
        })();
    });
});
