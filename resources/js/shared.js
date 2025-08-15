/// <reference path="./lib/jquery.js" />
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
