-- TalentsHUB Database Schema
-- Plataforma de Recrutamento e Seleção

CREATE DATABASE IF NOT EXISTS talentshub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE talentshub_db;

-- Tabela principal de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('candidato', 'empresa', 'admin') NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    email_verificado BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_tipo_usuario (tipo_usuario)
);

-- Tabela de candidatos
CREATE TABLE candidatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    telefone VARCHAR(20),
    data_nascimento DATE,
    genero ENUM('masculino', 'feminino', 'outro', 'nao_informar'),
    estado_civil ENUM('solteiro', 'casado', 'divorciado', 'viuvo', 'uniao_estavel'),
    endereco TEXT,
    cidade VARCHAR(100),
    estado VARCHAR(2),
    cep VARCHAR(10),
    linkedin VARCHAR(255),
    portfolio VARCHAR(255),
    resumo_profissional TEXT,
    pretensao_salarial DECIMAL(10,2),
    disponibilidade ENUM('imediata', '30_dias', '60_dias', '90_dias', 'negociavel'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_id (usuario_id)
);

-- Tabela de empresas
CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    cnpj VARCHAR(14) UNIQUE NOT NULL,
    razao_social VARCHAR(255),
    telefone VARCHAR(20),
    endereco TEXT,
    cidade VARCHAR(100),
    estado VARCHAR(2),
    cep VARCHAR(10),
    site VARCHAR(255),
    linkedin VARCHAR(255),
    descricao TEXT,
    setor VARCHAR(100),
    porte ENUM('micro', 'pequena', 'media', 'grande'),
    funcionarios INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_cnpj (cnpj)
);

-- Tabela de vagas
CREATE TABLE vagas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    requisitos TEXT,
    beneficios TEXT,
    salario_min DECIMAL(10,2),
    salario_max DECIMAL(10,2),
    tipo_contrato ENUM('clt', 'pj', 'estagio', 'trainee', 'freelancer'),
    modalidade ENUM('presencial', 'remoto', 'hibrido'),
    nivel_experiencia ENUM('estagiario', 'junior', 'pleno', 'senior', 'especialista'),
    area VARCHAR(100),
    localizacao VARCHAR(255),
    status ENUM('ativa', 'pausada', 'finalizada') DEFAULT 'ativa',
    data_limite DATE,
    visualizacoes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE,
    INDEX idx_empresa_id (empresa_id),
    INDEX idx_status (status),
    INDEX idx_area (area),
    INDEX idx_modalidade (modalidade)
);

-- Tabela de candidaturas
CREATE TABLE candidaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vaga_id INT NOT NULL,
    candidato_id INT NOT NULL,
    curriculo_path VARCHAR(500),
    carta_apresentacao TEXT,
    status ENUM('enviada', 'visualizada', 'em_analise', 'aprovada', 'rejeitada', 'entrevista_agendada') DEFAULT 'enviada',
    data_candidatura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_visualizacao TIMESTAMP NULL,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vaga_id) REFERENCES vagas(id) ON DELETE CASCADE,
    FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_candidatura (vaga_id, candidato_id),
    INDEX idx_vaga_id (vaga_id),
    INDEX idx_candidato_id (candidato_id),
    INDEX idx_status (status)
);

-- Tabela de experiências profissionais
CREATE TABLE experiencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidato_id INT NOT NULL,
    empresa VARCHAR(255) NOT NULL,
    cargo VARCHAR(255) NOT NULL,
    descricao TEXT,
    data_inicio DATE NOT NULL,
    data_fim DATE NULL,
    atual BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE CASCADE,
    INDEX idx_candidato_id (candidato_id)
);

-- Tabela de formações acadêmicas
CREATE TABLE formacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidato_id INT NOT NULL,
    instituicao VARCHAR(255) NOT NULL,
    curso VARCHAR(255) NOT NULL,
    nivel ENUM('ensino_medio', 'tecnico', 'superior', 'pos_graduacao', 'mestrado', 'doutorado'),
    status ENUM('concluido', 'cursando', 'trancado'),
    data_inicio DATE,
    data_conclusao DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE CASCADE,
    INDEX idx_candidato_id (candidato_id)
);

-- Tabela de habilidades
CREATE TABLE habilidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidato_id INT NOT NULL,
    habilidade VARCHAR(100) NOT NULL,
    nivel ENUM('basico', 'intermediario', 'avancado', 'expert'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE CASCADE,
    INDEX idx_candidato_id (candidato_id)
);

-- Tabela de favoritos (candidatos salvando vagas)
CREATE TABLE favoritos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    candidato_id INT NOT NULL,
    vaga_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE CASCADE,
    FOREIGN KEY (vaga_id) REFERENCES vagas(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorito (candidato_id, vaga_id),
    INDEX idx_candidato_id (candidato_id),
    INDEX idx_vaga_id (vaga_id)
);

-- Tabela de notificações
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('candidatura', 'status', 'vaga', 'sistema', 'empresa', 'candidato') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT NOT NULL,
    dados_extra JSON,
    lida BOOLEAN DEFAULT FALSE,
    lida_em TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_tipo (tipo),
    INDEX idx_lida (lida),
    INDEX idx_created_at (created_at)
);

-- Tabela de logs do sistema
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao VARCHAR(100) NOT NULL,
    descricao TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_acao (acao),
    INDEX idx_created_at (created_at)
);

-- Inserir usuário administrador padrão
INSERT INTO usuarios (nome, email, senha, tipo_usuario, ativo, email_verificado) 
VALUES ('Administrador TalentsHUB', 'admin@talentshub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', TRUE, TRUE);

-- Inserir empresa de exemplo
INSERT INTO usuarios (nome, email, senha, tipo_usuario, ativo, email_verificado) 
VALUES ('TechCorp Brasil', 'contato@techcorp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'empresa', TRUE, TRUE);

INSERT INTO empresas (usuario_id, cnpj, razao_social, telefone, descricao, setor, porte) 
VALUES (2, '12345678000195', 'TechCorp Brasil Ltda', '11999999999', 'Empresa de tecnologia inovadora', 'Tecnologia', 'media');

-- Inserir candidato de exemplo
INSERT INTO usuarios (nome, email, senha, tipo_usuario, ativo, email_verificado) 
VALUES ('João Silva', 'joao.silva@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'candidato', TRUE, TRUE);

INSERT INTO candidatos (usuario_id, telefone, data_nascimento, resumo_profissional) 
VALUES (3, '11988888888', '1990-05-15', 'Desenvolvedor com 5 anos de experiência em PHP e MySQL');
