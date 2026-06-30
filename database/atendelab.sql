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
    nome VARCHAR(100) NOT NULL,
    documento VARCHAR(20) UNIQUE,
    telefone VARCHAR(20),
    email VARCHAR(100),
    curso VARCHAR(100),
    periodo VARCHAR(100),
    observacoes TEXT,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo'
);

-- Tabela: tipos_atendimentos
CREATE TABLE tipos_atendimentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo'
);

-- Tabela: atendimentos
CREATE TABLE atendimentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pessoa_id INT NOT NULL,
    tipo_atendimento_id INT NOT NULL,
    usuario_id INT NOT NULL,
    descricao TEXT,
    status ENUM('aberto', 'em_andamento', 'concluido') DEFAULT 'aberto',
    data_atendimento DATE NOT NULL,
    horario_atendimento TIME NOT NULL,
    observacao_final TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pessoa_id) REFERENCES pessoas(id),
    FOREIGN KEY (tipo_atendimento_id) REFERENCES tipos_atendimentos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Usuário admin de teste (senha: 123456)
INSERT INTO usuarios (nome, email, senha, perfil, status) VALUES
('Administrador', 'admin@atendelab.com', '$2y$10$ZbwdxCDumlnqh3TsK0e1m.VNy6sO8g8/o24cHPD8oM10BqCvFr/6K', 'admin', 'ativo');
