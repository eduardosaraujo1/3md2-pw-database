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
