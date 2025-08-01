# Sistema de Cadastro de Contato

Este projeto é um sistema de cadastro de contato com validação de dados em tempo real no front-end e verificação de unicidade no back-end, utilizando HTML, jQuery, PHP (com PDO) e MySQL.

## Como Executar

1. **Importe o banco de dados:**

    - Execute o arquivo `script.sql` no seu SGBD (como MySQL Workbench, phpMyAdmin, etc).

2. **Configure as credenciais do banco de dados:**
    - Edite os arquivos abaixo com os dados corretos de conexão (host, usuário, senha e nome do banco):
        - [`php/contato/store.php`](php/contato/store.php)
        - [`php/user/store.php`](php/user/store.php)

## Descrição Geral

A aplicação inicia com um formulário de cadastro estilizado contendo os seguintes campos:

-   Nome
-   Login
-   Senha
-   Confirmar Senha
-   E-mail
-   Telefone
-   Upload de Foto

![Exemplo do formulário](/docs/sample.png)

O botão de envio começa **desabilitado** e só é ativado **quando todas as regras de validação forem atendidas**.

### Regras de Validação (Front-end com jQuery)

1. **Todos os campos obrigatórios** (Nome, Login, Senha, Confirmar Senha, E-mail, Telefone) devem estar preenchidos com valores válidos.
2. O campo **E-mail** deve conter o caractere `@`.
3. A **Senha** deve:
    - Ter no mínimo 8 caracteres;
    - Conter **1 letra maiúscula**, **1 letra minúscula** e **1 caractere especial**.
4. O campo **Confirmar Senha** deve ser igual ao campo **Senha**.

⚠️ A validação deve ser implementada usando jQuery. O botão só será ativado quando **todas** as regras forem verdadeiras.

## Validação no Back-End (PHP + PDO)

Ao submeter o formulário:

1. O PHP conecta-se ao MySQL via **PDO**.
2. Antes de inserir os dados:
    - Verifica se **o e-mail já está cadastrado** na tabela `tb_contato`. Se sim, retorna um erro.
    - Verifica se **o login já está cadastrado** na tabela `tb_contato`. Se sim, retorna um erro.
3. Se os dados forem válidos e únicos, realiza o `INSERT` no banco.

**Obs.:** O caminho da foto enviada deve ser salvo no banco de dados, mas **não é necessário armazenar o arquivo da imagem** em disco.

## ️Estrutura do Banco de Dados

```sql
DROP SCHEMA IF EXISTS pw_atividade;
CREATE SCHEMA IF NOT EXISTS pw_atividade;

USE pw_atividade;

CREATE TABLE tb_usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    login VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE tb_contato (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    login VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(255) NOT NULL,
    foto VARCHAR(255)
);

INSERT INTO tb_usuario (login, email, senha) VALUES
('didi', 'didi@gmail.com', '1234');
```

# ROadmap

Criar tela de login, onde passa o login e senha para validar no banco, depois armazenar na sessão o usuário e encaminhar para tela inicial. Se der erro, exibir mensagem de erro (via AJAX)
Criar tela home com os dados do usuário listados
