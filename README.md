# Sistema de Cadastro de Contato

Este projeto é um sistema de cadastro de contato com validação de dados em tempo real no front-end e verificação de unicidade no back-end, utilizando HTML, jQuery, PHP (com PDO) e MySQL.

## Como Executar

1. Clone o projeto na pasta `/xampp/htdocs`
2. Opcionalmente, troque o banco de dados de `sqlite` para `mysql` pelo arquivo [config/database.php](./config/database.php)
3. Inicie o servidor

## Banco de Dados padrão

```sql
DROP SCHEMA IF EXISTS learning;
CREATE SCHEMA IF NOT EXISTS learning;
USE learning;

CREATE TABLE IF NOT EXISTS tb_contato(
    id int primary key auto_increment,
    nome VARCHAR(255) NOT NULL,
    login VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(255) NOT NULL,
    foto VARCHAR(255)
);

INSERT INTO tb_contato VALUES (NULL, "Admin", "admin", "$2a$12$IYe6qvlevtzmCxu4zjkIIuLmrPMIvBwmhl3YApHE7fuxI9cadkesW", 'admin@gmail.com', '11951490211', NULL); -- Senha: admin
```
