/// <reference path="../lib/jquery.js" />
/// <reference path="../shared.js" />

/**
 * @typedef {Object} User
 * @property {number} id
 * @property {string} nome
 * @property {string} login
 * @property {string} email
 * @property {string} telefone
 */

class UserRepository {
    /**
     * Fetch all users from the server.
     * @returns {Promise<User[]>}
     */
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
     * @param {User[]} users
     */
    renderTable(users) {
        const tbody = this.tableElement.find("tbody");
        tbody.empty();

        users.forEach((user) => {
            const row = this.generateRowHTML(user);
            tbody.append(row);
        });
    }

    /**
     * Gera o HTML para uma linha da tabela de usuários.
     * @param {User} user
     * @returns {string}
     */
    generateRowHTML(user) {
        return `
            <tr>
                <td>${user.id}</td>
                <td>${user.nome}</td>
                <td>${user.login}</td>
                <td>${user.email}</td>
                <td>${user.telefone}</td>
                <td>
                    <button data-bs-toggle="modal" data-bs-target="#editUserModal" type="button" class="btn btn-sm btn-primary me-1 js-btn-edit" title="Editar" data-target="${user.id}">
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

    $("#userTable").on("click", ".js-btn-delete", async (event) => {
        const userId = $(event.currentTarget).data("target");
        if (!confirm("Tem certeza de que deseja excluir este usuário?")) {
            return;
        }
        try {
            const formData = new FormData();
            formData.append("id", userId);
            const response = await sendFormData({
                endpoint: `/users/destroy`,
                method: "POST",
                formData: formData,
            });
            console.log("Usuário excluído com sucesso:", response);
            // Recarregar a tabela de usuários
            $(document).trigger("reloadUserTable");
        } catch (xhr) {
            console.error("Erro ao excluir usuário:", xhr.responseJSON?.["message"] ?? "Erro desconhecido");
        }
    });
});
