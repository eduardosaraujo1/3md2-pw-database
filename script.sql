DROP SCHEMA IF EXISTS learning;
CREATE SCHEMA IF NOT EXISTS learning;
USE learning;

CREATE TABLE IF NOT EXISTS tb_usuario(
    id int primary key auto_increment,
    login VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS tb_contato(
    id int primary key auto_increment,
    nome VARCHAR(255) NOT NULL,
    login VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(255) NOT NULL,
    foto VARCHAR(255)
);
