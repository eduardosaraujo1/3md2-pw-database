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

class UserListController {
    constructor(containerId) {
        this.containerId = containerId;
        this.containerElement = $(`#${containerId}`);
    }

    /**
     * @param {User[]} users
     */
    renderCards(users) {
        // Clear existing cards
        this.containerElement.empty();

        users.forEach((user) => {
            const card = this.generateCardHTML(user);
            this.containerElement.append(card);
        });
    }

    /**
     * Gera o HTML para um card de usuário.
     * @param {User} user
     * @returns {string}
     */
    generateCardHTML(user) {
        const profileImageUrl = `/users/profile?id=${user.id}`;

        return `
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <p class="text-muted small text-center fw-semibold mb-1 js-id-field" data-target="${user.id}">${user.id}</p>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <img src="${profileImageUrl}"
                                 class="rounded-circle"
                                 width="80"
                                 height="80"
                                 alt="Avatar de ${user.nome}"
                                 onerror="this.src='/resources/assets/blank.png'">
                        </div>
                        <h6 class="card-title text-secondary mb-1 js-nome-field" data-target="${user.id}">${user.nome}</h6>
                        <p class="text-muted small mb-1 js-login-field" data-target="${user.id}">${user.login}</p>
                        <p class="text-muted small mb-1 js-email-field" data-target="${user.id}">${user.email}</p>
                        <p class="text-muted small mb-1 js-telefone-field" data-target="${user.id}">${user.telefone}</p>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button"
                                    class="btn btn-sm btn-danger js-btn-delete"
                                    title="Excluir"
                                    data-target="${user.id}">
                                Apagar
                            </button>
                            <button onclick="window.onEditPress(event)"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editUserModal"
                                    type="button"
                                    class="btn btn-sm btn-warning js-btn-edit"
                                    title="Editar"
                                    data-target="${user.id}">
                                Editar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}

$(() => {
    const userRepository = new UserRepository();
    const userListController = new UserListController("userCards");

    async function loadUsers() {
        try {
            const users = await userRepository.fetchUsers();
            userListController.renderCards(users);
        } catch (error) {
            console.error("Erro ao carregar usuários:", error);
        }
    }

    $(document).on("reloadUserTable", () => {
        loadUsers();
    });
    loadUsers();

    $("#userCards").on("click", ".js-btn-delete", async (event) => {
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
