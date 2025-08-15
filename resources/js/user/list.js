/// <reference path="../lib/jquery.js" />
/// <reference path="../shared.js" />

class UserRepository {
    async fetchUsers() {
        const response = await sendFormData({
            endpoint: "/users",
            method: "GET",
            formData: null,
            onSuccess: (response) => resolve(response),
            onError: (xhr) => reject(xhr),
        });

        if (!Array.isArray(response)) {
            throw new Error("Resposta inválida do servidor: esperado um array de usuários.");
        }

        return response;
    }
}

class UserTableController {
    constructor(tableId) {
        this.tableId = tableId;
        this.tableElement = $(`#${tableId}`);
    }

    /**
     * @param {Array} users
     */
    renderTable(users) {
        const tbody = this.tableElement.find("tbody");
        tbody.empty();

        users.forEach((user) => {
            const row = this.generateRowHTML(user);
            tbody.append(row);
        });
    }

    generateRowHTML(user) {
        return `
            <tr>
                <td>${user.id}</td>
                <td>${user.nome}</td>
                <td>${user.login}</td>
                <td>${user.email}</td>
                <td>${user.telefone}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-primary me-1 js-btn-edit" title="Editar" data-target="${user.id}">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger js-btn-delete" title="Excluir" data-target="${user.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }
}

$(() => {
    const userRepository = new UserRepository();
    const userTableController = new UserTableController("userTable", userRepository);

    async function loadUsers() {
        try {
            const users = await userRepository.fetchUsers();
            userTableController.renderTable(users);
        } catch (error) {
            console.error("Erro ao carregar usuários:", error);
        }
    }

    $(document).on("reloadUserTable", () => {
        loadUsers();
    });
    loadUsers();

    $("#userTable").on("click", ".js-btn-edit", (event) => {
        const userId = $(event.currentTarget).data("target");
        console.log("Editar usuário:", userId);
        // TODO: Adicionar lógica para editar usuário
    });

    $("#userTable").on("click", ".js-btn-delete", (event) => {
        const userId = $(event.currentTarget).data("target");
        console.log("Excluir usuário:", userId);
        // TODO: Adicionar lógica para excluir usuário
    });
});
