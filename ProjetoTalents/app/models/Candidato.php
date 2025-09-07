<?php
// Arquivo: app/models/Candidato.php
// TalentsHUB - Modelo de Candidato

class Candidato {
    private $conn;

    public function __construct() {
        $this->conn = getDbConnection();
    }

    public function create($usuario_id, $telefone, $data_nascimento, $genero, $estado_civil, $endereco, $cidade, $estado, $cep, $linkedin, $portfolio, $resumo_profissional, $pretensao_salarial, $disponibilidade) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO candidatos (
                    usuario_id, telefone, data_nascimento, genero, estado_civil, endereco, cidade, estado, cep,
                    linkedin, portfolio, resumo_profissional, pretensao_salarial, disponibilidade, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            return $stmt->execute([
                $usuario_id, $telefone, $data_nascimento, $genero, $estado_civil, $endereco, $cidade, $estado, $cep,
                $linkedin, $portfolio, $resumo_profissional, $pretensao_salarial, $disponibilidade
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao criar candidato: " . $e->getMessage());
            return false;
        }
    }

    public function findById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT c.*, u.nome, u.email, u.tipo_usuario
                FROM candidatos c
                JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro ao buscar candidato por ID: " . $e->getMessage());
            return false;
        }
    }

    public function findByUsuarioId($usuario_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT c.*, u.nome, u.email, u.tipo_usuario
                FROM candidatos c
                JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.usuario_id = ?
            ");
            $stmt->execute([$usuario_id]);
            $result = $stmt->fetch();
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar candidato por usuário: " . $e->getMessage());
            return null;
        }
    }

    public function update($id, $usuario_id, $telefone, $data_nascimento, $genero, $estado_civil, $endereco, $cidade, $estado, $cep, $linkedin, $portfolio, $resumo_profissional, $pretensao_salarial, $disponibilidade) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE candidatos SET 
                    telefone = ?, data_nascimento = ?, genero = ?, estado_civil = ?, endereco = ?, 
                    cidade = ?, estado = ?, cep = ?, linkedin = ?, portfolio = ?, 
                    resumo_profissional = ?, pretensao_salarial = ?, disponibilidade = ?, updated_at = NOW()
                WHERE id = ? AND usuario_id = ?
            ");
            
            return $stmt->execute([
                $telefone, $data_nascimento, $genero, $estado_civil, $endereco, $cidade, $estado, $cep,
                $linkedin, $portfolio, $resumo_profissional, $pretensao_salarial, $disponibilidade, $id, $usuario_id
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar candidato: " . $e->getMessage());
            return false;
        }
    }

    public function getAll($filters = [], $limit = 20, $offset = 0) {
        try {
            $where = "WHERE 1=1";
            $params = [];
            
            // Aplicar filtros
            if (!empty($filters['cidade'])) {
                $where .= " AND c.cidade LIKE ?";
                $params[] = "%" . $filters['cidade'] . "%";
            }
            
            if (!empty($filters['estado'])) {
                $where .= " AND c.estado = ?";
                $params[] = $filters['estado'];
            }
            
            if (!empty($filters['disponibilidade'])) {
                $where .= " AND c.disponibilidade = ?";
                $params[] = $filters['disponibilidade'];
            }
            
            if (!empty($filters['search'])) {
                $where .= " AND (u.nome LIKE ? OR c.resumo_profissional LIKE ?)";
                $search = "%" . $filters['search'] . "%";
                $params[] = $search;
                $params[] = $search;
            }
            
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->conn->prepare("
                SELECT c.*, u.nome, u.email,
                       (SELECT COUNT(*) FROM candidaturas cand WHERE cand.candidato_id = c.id) as total_candidaturas,
                       (SELECT GROUP_CONCAT(h.habilidade SEPARATOR ', ') FROM habilidades h WHERE h.candidato_id = c.id) as habilidades
                FROM candidatos c
                JOIN usuarios u ON c.usuario_id = u.id
                $where
                ORDER BY c.created_at DESC
                LIMIT ? OFFSET ?
            ");
            
            $stmt->execute($params);
            $result = $stmt->fetchAll();
            return $result !== false ? $result : [];
        } catch (PDOException $e) {
            error_log("Erro ao buscar candidatos: " . $e->getMessage());
            return [];
        }
    }

    public function getExperiencias($candidato_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM experiencias 
                WHERE candidato_id = ? 
                ORDER BY data_inicio DESC
            ");
            $stmt->execute([$candidato_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar experiências: " . $e->getMessage());
            return false;
        }
    }

    public function getFormacoes($candidato_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM formacoes 
                WHERE candidato_id = ? 
                ORDER BY data_inicio DESC
            ");
            $stmt->execute([$candidato_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar formações: " . $e->getMessage());
            return false;
        }
    }

    public function getHabilidades($candidato_id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM habilidades 
                WHERE candidato_id = ? 
                ORDER BY nivel DESC, habilidade ASC
            ");
            $stmt->execute([$candidato_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar habilidades: " . $e->getMessage());
            return false;
        }
    }

    public function addExperiencia($candidato_id, $empresa, $cargo, $descricao, $data_inicio, $data_fim, $atual) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO experiencias (candidato_id, empresa, cargo, descricao, data_inicio, data_fim, atual, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            return $stmt->execute([$candidato_id, $empresa, $cargo, $descricao, $data_inicio, $data_fim, $atual]);
        } catch (PDOException $e) {
            error_log("Erro ao adicionar experiência: " . $e->getMessage());
            return false;
        }
    }

    public function addFormacao($candidato_id, $instituicao, $curso, $nivel, $status, $data_inicio, $data_conclusao) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO formacoes (candidato_id, instituicao, curso, nivel, status, data_inicio, data_conclusao, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            return $stmt->execute([$candidato_id, $instituicao, $curso, $nivel, $status, $data_inicio, $data_conclusao]);
        } catch (PDOException $e) {
            error_log("Erro ao adicionar formação: " . $e->getMessage());
            return false;
        }
    }

    public function addHabilidade($candidato_id, $habilidade, $nivel) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO habilidades (candidato_id, habilidade, nivel, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            return $stmt->execute([$candidato_id, $habilidade, $nivel]);
        } catch (PDOException $e) {
            error_log("Erro ao adicionar habilidade: " . $e->getMessage());
            return false;
        }
    }

    public function getStats() {
        try {
            $stats = [];
            
            // Total de candidatos
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM candidatos");
            $stmt->execute();
            $stats['total_candidatos'] = $stmt->fetch()['total'];
            
            // Candidatos por estado
            $stmt = $this->conn->prepare("
                SELECT estado, COUNT(*) as total 
                FROM candidatos 
                WHERE estado IS NOT NULL AND estado != '' 
                GROUP BY estado 
                ORDER BY total DESC 
                LIMIT 10
            ");
            $stmt->execute();
            $stats['por_estado'] = $stmt->fetchAll();
            
            // Candidatos por disponibilidade
            $stmt = $this->conn->prepare("
                SELECT disponibilidade, COUNT(*) as total 
                FROM candidatos 
                WHERE disponibilidade IS NOT NULL 
                GROUP BY disponibilidade 
                ORDER BY total DESC
            ");
            $stmt->execute();
            $stats['por_disponibilidade'] = $stmt->fetchAll();
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Erro ao buscar estatísticas de candidatos: " . $e->getMessage());
            return false;
        }
    }
}
?>

