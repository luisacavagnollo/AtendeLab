-- ===========================================
-- AtendeLab - Script de criação do banco
-- ===========================================

CREATE DATABASE IF NOT EXISTS atendelab
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE atendelab;

-- Tabela: usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('admin', 'atendente') DEFAULT 'atendente',
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela: pessoas
CREATE TABLE pessoas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    documento VARCHAR(20) UNIQUE,
    telefone VARCHAR(20),
    curso VARCHAR(100),
    periodo VARCHAR(100),
    status VARCHAR(100)
);

-- Tabela: tipos_atendimentos
CREATE TABLE tipos_atendimentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    descricao TEXT,
    status ENUM('ativo', 'inativo')
);

-- Tabela: atendimentos
CREATE TABLE atendimentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pessoa_id INT,
    tipo_atendimento INT,
    usuario_id INT,
    data_atendimento DATE,
    hora_atendimento TIME,
    descricao TEXT,
    observacao TEXT,
    status ENUM('aberto', 'em andamento', 'concluido'),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pessoa_id) REFERENCES pessoas(id),
    FOREIGN KEY (tipo_atendimento) REFERENCES tipos_atendimentos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Usuário admin de teste (senha: 123456)
INSERT INTO usuarios (nome, email, senha, perfil, status) VALUES
('Administrador', 'admin@atendelab.com', '$2y$10$ZbwdxCDumlnqh3TsK0e1m.VNy6sO8g8/o24cHPD8oM10BqCvFr/6K', 'admin', 'ativo');
