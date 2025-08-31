# Sistema de Cadastro de Contato

Este projeto é um sistema de cadastro de contato com validação de dados em tempo real no front-end e verificação de unicidade no back-end, utilizando HTML, jQuery, PHP (com PDO) e MySQL.

## Como Executar

1. Clone o projeto na pasta `/xampp/htdocs`
    - Importante: não coloque o projeto dentro de uma subpasta (como C:\xampp\htdocs\3md2-pw-database), pois isso quebra o sistema de URL.
2. Opcionalmente, troque o banco de dados de `sqlite` para `mysql` pelo arquivo [config/database.php](./config/database.php)
3. Inicie o servidor

## Framework

A estrutura da aplicação é um hibrido entre os padrões vistos em [PHP Framework PRO - Gary Clarke](https://www.youtube.com/watch?v=5FxuPuJkCGs&list=PLQH1-k79HB3-0SKspp8814ZI1GIqRYLAu), a estrutura do framework Laravel, e arquitetura Model-View-Controller.

Para aprender a utilizar o framework, veja a documentação dele em [framework.md](docs/framework.md).

## Tela de Login

Para fazer login, caso nenhuma alteração tenha sido feita em [mysql.sql](database/migrations/mysql.sql) e [sqlite.sql](database/migrations/sqlite.sql), o login é `admin` e a senha também é `admin`

## Esquema de Banco de Dados

-   **MySQL**:

```sql
CREATE TABLE IF NOT EXISTS tb_contato(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    login VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(255) NOT NULL,
    foto VARCHAR(255)
);

INSERT INTO tb_contato VALUES
    (NULL, "Admin", "admin", "$2a$12$IYe6qvlevtzmCxu4zjkIIuLmrPMIvBwmhl3YApHE7fuxI9cadkesW", 'admin@gmail.com', '11951490211', NULL); -- Senha: admin
```

-   **SQLite**:

```sql
CREATE TABLE IF NOT EXISTS tb_contato(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    login TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    telefone TEXT NOT NULL,
    foto TEXT
);

INSERT INTO tb_contato VALUES
    (NULL, "Admin", "admin", "$2a$12$IYe6qvlevtzmCxu4zjkIIuLmrPMIvBwmhl3YApHE7fuxI9cadkesW", 'admin@gmail.com', '11951490211', NULL); -- Senha: admin
```

# Roadmap

-   [x] Endpoint para pegar a imagem do usuário
-   [ ] Frontend com design de cards conforme imagem Concept.png
-   [ ] Frontend com módulos ES6 no main.js
-   [ ] Refatorar 'core' para ter pastas:
    -   Application - Lida com classe Container e classe Application, que vai carregar functions.php e definir PROJECT_ROOT
    -   Application/Providers - Local onde CoreServiceProvider.php e Provider.php fica
    -   Database/Connection - Lidar com conexão ao banco de dados
    -   Database/Repository.php - Interface que os repositórios PHP devem obedecer
    -   Http - Possui na sua raiz Kernel.php
    -   Http/Routing - Router usado pelo Kernel.php
    -   Http/Middleware - Caso seja adicionado, uma pasta para Middleware fica aqui
    -   Http/Support - Local de Request e Response
    -   Services - Declaração das classes que utilizam
    -   Facades/Facade.php - Uso de métodos estáticos para melhorar a sintaxe de um Service Container
    -   ResponseFactory colocar direto no Response como classe estática
-   [ ] Refatorar 'app' para ter pastas:
    -   Http/Controllers - Controllers da aplicação
    -   Http/FormRequest - Classes que herdam de Request e possuem método ->validate (não implementado ainda, mas Request base terá o método validate e pode receber um callback para realizar validação e filtragem)
    -   Data/Models - Local dos modelos da aplicação
    -   Data/Repositores - Repositórios para interagir com BD
    -   Data/Exceptions - Erros utilizados em repositórios ou models
    -   Domain/Services - Utilitários que o próprio usuário escreve
    -   Domain/Exceptions - Erros criados pelo usuário utilizados em serviços
    -   Providers - Providers customizados que servem para configurar o service container
-   [ ] Criar helper "view" e tirar essa responsabilidade do helper "response"
-   [ ] Adicionar suporte para outros verbos HTTP (PUT/PATCH e DELETE)
-   [ ] Adicionar suporte para parâmetros de rotas (`/users/{id}`)
-   [ ] Criar diretório /public e endpoint /user/{id}/foto para melhorar segurança
-   [ ] Autenticação e autorização de ações via autenticação
