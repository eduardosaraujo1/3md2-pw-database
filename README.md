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

- **MySQL**:

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

- **SQLite**:

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

- [ ] Endpoint para pegar a imagem do usuário
- [ ] Frontend com design de cards conforme imagem Concept.png
- [ ] Adicionar suporte para outros verbos HTTP (PUT/PATCH e DELETE)
- [ ] Adicionar suporte para parâmetros de rotas (`/users/{id}`)
- [ ] Criar diretório /public e endpoint /user/{id}/foto para melhorar segurança
- [ ] Autenticação e autorização de ações via autenticação
