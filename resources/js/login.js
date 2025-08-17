/// <reference path="./lib/jquery.js" />
/// <reference path="./shared.js" />

$(() => {
    const formController = new FormController(["login", "senha"]);

    const inputs = $("input");
    const submitButton = $("#submitButton");
    const errorMessage = $("#errorMessage");

    function updateButtonState() {
        const isComplete = formController.checkComplete();
        submitButton.prop("disabled", !isComplete);
    }

    inputs.on("keydown", () => {
        formController.pullFromDOM();
        updateButtonState();
    });
    updateButtonState();

    submitButton.on("click", async () => {
        try {
            const res = await formController.submit("/signin");
            if (res.status === "error") {
                errorMessage.text(res.message);
            } else if (res.status === "success") {
                window.location.href = "/";
            }
        } catch (err) {
            if (err && typeof err === "object" && "responseJSON" in err) {
                const res = err.responseJSON;
                errorMessage.text(res?.message ?? "Erro interno no servidor");
            } else {
                throw err;
            }
        }
    });
});
