DROP SCHEMA IF EXISTS learning;
CREATE SCHEMA IF NOT EXISTS learning;

use learning;

CREATE TABLE tb_usuario(
    id int primary key auto_increment,
    login VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE tb_contato(
    id int primary key auto_increment,
    nome VARCHAR(255) NOT NULL,
    login VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefone VARCHAR(255) NOT NULL,
    foto VARCHAR(255)
);

INSERT INTO tb_usuario (login, email, senha) VALUES
('didi', 'didi@gmail.com', '1234');