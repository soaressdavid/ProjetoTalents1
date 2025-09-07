<?php
// Arquivo: app/models/User.php
// TalentsHUB - Modelo de Usuário

class User {
    private $conn;

    public function __construct() {
        $this->conn = getDbConnection();
    }

    public function findByEmail($email) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ? AND ativo = 1");
            $stmt->execute([$email]);
            $result = $stmt->fetch();
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário por email: " . $e->getMessage());
            return null;
        }
    }

    public function findById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ? AND ativo = 1");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário por ID: " . $e->getMessage());
            return null;
        }
    }

    public function create($nome, $email, $senha, $tipo_usuario) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo_usuario, created_at) VALUES (?, ?, ?, ?, NOW())");
            return $stmt->execute([$nome, $email, $senha, $tipo_usuario]);
        } catch (PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $nome, $email, $senha = null) {
        try {
            if ($senha) {
                $sql = "UPDATE usuarios SET nome = ?, email = ?, senha = ?, updated_at = NOW() WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([$nome, $email, $senha, $id]);
            } else {
                $sql = "UPDATE usuarios SET nome = ?, email = ?, updated_at = NOW() WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([$nome, $email, $id]);
            }
        } catch (PDOException $e) {
            error_log("Erro ao atualizar perfil: " . $e->getMessage());
            return false;
        }
    }

    public function deactivate($id) {
        try {
            $stmt = $this->conn->prepare("UPDATE usuarios SET ativo = 0, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erro ao desativar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function getAllUsers($limit = 50, $offset = 0) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE ativo = 1 ORDER BY created_at DESC LIMIT ? OFFSET ?");
            $stmt->execute([$limit, $offset]);
            $result = $stmt->fetchAll();
            return $result !== false ? $result : [];
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuários: " . $e->getMessage());
            return [];
        }
    }

    public function getUsersByType($tipo_usuario, $limit = 50, $offset = 0) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE tipo_usuario = ? AND ativo = 1 ORDER BY created_at DESC LIMIT ? OFFSET ?");
            $stmt->execute([$tipo_usuario, $limit, $offset]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuários por tipo: " . $e->getMessage());
            return false;
        }
    }

    public function getStats() {
        try {
            $stats = [];
            
            // Total de usuários
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM usuarios WHERE ativo = 1");
            $stmt->execute();
            $stats['total_usuarios'] = $stmt->fetch()['total'];
            
            // Usuários por tipo
            $stmt = $this->conn->prepare("SELECT tipo_usuario, COUNT(*) as total FROM usuarios WHERE ativo = 1 GROUP BY tipo_usuario");
            $stmt->execute();
            $stats['por_tipo'] = $stmt->fetchAll();
            
            // Usuários cadastrados hoje
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM usuarios WHERE DATE(created_at) = CURDATE() AND ativo = 1");
            $stmt->execute();
            $stats['cadastros_hoje'] = $stmt->fetch()['total'];
            
            // Usuários cadastrados este mês
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM usuarios WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND ativo = 1");
            $stmt->execute();
            $stats['cadastros_mes'] = $stmt->fetch()['total'];
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Erro ao buscar estatísticas: " . $e->getMessage());
            return false;
        }
    }

    public function verifyEmail($id) {
        try {
            $stmt = $this->conn->prepare("UPDATE usuarios SET email_verificado = 1, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erro ao verificar email: " . $e->getMessage());
            return false;
        }
    }

    public function changePassword($id, $nova_senha) {
        try {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE usuarios SET senha = ?, updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$senha_hash, $id]);
        } catch (PDOException $e) {
            error_log("Erro ao alterar senha: " . $e->getMessage());
            return false;
        }
    }
}
?>