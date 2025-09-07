<?php
// Arquivo: app/models/Candidatura.php
// TalentsHUB - Modelo de Candidatura

class Candidatura {
    private $conn;

    public function __construct() {
        $this->conn = getDbConnection();
    }

    public function create($vaga_id, $candidato_id, $curriculo_path = null, $carta_apresentacao = null) {
        try {
            // Verificar se já existe candidatura
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM candidaturas WHERE vaga_id = ? AND candidato_id = ?");
            $stmt->execute([$vaga_id, $candidato_id]);
            if ($stmt->fetchColumn() > 0) {
                return false; // Candidatura já existe
            }
            
            $stmt = $this->conn->prepare("
                INSERT INTO candidaturas (vaga_id, candidato_id, curriculo_path, carta_apresentacao, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            return $stmt->execute([$vaga_id, $candidato_id, $curriculo_path, $carta_apresentacao]);
        } catch(PDOException $e) {
            error_log("Erro ao criar candidatura: " . $e->getMessage());
            return false;
        }
    }

    public function findById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT c.*, v.titulo as vaga_titulo, v.empresa_id,
                       u.nome as candidato_nome, u.email as candidato_email,
                       e.razao_social as empresa_nome
                FROM candidaturas c
                JOIN vagas v ON c.vaga_id = v.id
                JOIN candidatos cand ON c.candidato_id = cand.id
                JOIN usuarios u ON cand.usuario_id = u.id
                JOIN empresas e ON v.empresa_id = e.id
                WHERE c.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Erro ao buscar candidatura por ID: " . $e->getMessage());
            return false;
        }
    }

    public function findByCandidatoId($candidato_id, $limit = 20, $offset = 0) {
        try {
            $stmt = $this->conn->prepare("
                SELECT c.*, v.titulo, v.localizacao, v.status as vaga_status,
                       e.razao_social as empresa_nome
                FROM candidaturas c
                JOIN vagas v ON c.vaga_id = v.id
                JOIN empresas e ON v.empresa_id = e.id
                WHERE c.candidato_id = ?
                ORDER BY c.data_candidatura DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$candidato_id, $limit, $offset]);
            $result = $stmt->fetchAll();
            return $result !== false ? $result : [];
        } catch(PDOException $e) {
            error_log("Erro ao buscar candidaturas por candidato: " . $e->getMessage());
            return [];
        }
    }

    public function findByVagaId($vaga_id, $limit = 20, $offset = 0) {
        try {
            $stmt = $this->conn->prepare("
                SELECT c.*, u.nome as candidato_nome, u.email as candidato_email,
                       cand.telefone, cand.data_nascimento
                FROM candidaturas c
                JOIN candidatos cand ON c.candidato_id = cand.id
                JOIN usuarios u ON cand.usuario_id = u.id
                WHERE c.vaga_id = ?
                ORDER BY c.data_candidatura DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$vaga_id, $limit, $offset]);
            $result = $stmt->fetchAll();
            return $result !== false ? $result : [];
        } catch(PDOException $e) {
            error_log("Erro ao buscar candidaturas por vaga: " . $e->getMessage());
            return [];
        }
    }

    public function updateStatus($id, $status, $observacoes = null) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE candidaturas 
                SET status = ?, observacoes = ?, 
                    data_visualizacao = CASE WHEN ? = 'visualizada' AND data_visualizacao IS NULL THEN NOW() ELSE data_visualizacao END,
                    updated_at = NOW()
                WHERE id = ?
            ");
            return $stmt->execute([$status, $observacoes, $status, $id]);
        } catch(PDOException $e) {
            error_log("Erro ao atualizar status da candidatura: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id, $candidato_id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM candidaturas WHERE id = ? AND candidato_id = ?");
            return $stmt->execute([$id, $candidato_id]);
        } catch(PDOException $e) {
            error_log("Erro ao deletar candidatura: " . $e->getMessage());
            return false;
        }
    }

    public function checkIfAlreadyApplied($candidato_id, $vaga_id) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM candidaturas WHERE candidato_id = ? AND vaga_id = ?");
            $stmt->execute([$candidato_id, $vaga_id]);
            return $stmt->fetchColumn() > 0;
        } catch(PDOException $e) {
            error_log("Erro ao verificar candidatura: " . $e->getMessage());
            return false;
        }
    }

    public function getStats($empresa_id = null) {
        try {
            $stats = [];
            $where = $empresa_id ? "WHERE v.empresa_id = ?" : "";
            $params = $empresa_id ? [$empresa_id] : [];
            
            // Total de candidaturas
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as total 
                FROM candidaturas c
                JOIN vagas v ON c.vaga_id = v.id
                $where
            ");
            $stmt->execute($params);
            $stats['total_candidaturas'] = $stmt->fetch()['total'];
            
            // Candidaturas por status
            $stmt = $this->conn->prepare("
                SELECT c.status, COUNT(*) as total 
                FROM candidaturas c
                JOIN vagas v ON c.vaga_id = v.id
                $where
                GROUP BY c.status
            ");
            $stmt->execute($params);
            $stats['por_status'] = $stmt->fetchAll();
            
            // Candidaturas hoje
            $where_today = $empresa_id ? "WHERE v.empresa_id = ? AND DATE(c.data_candidatura) = CURDATE()" : "WHERE DATE(c.data_candidatura) = CURDATE()";
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as total 
                FROM candidaturas c
                JOIN vagas v ON c.vaga_id = v.id
                $where_today
            ");
            $stmt->execute($params);
            $stats['candidaturas_hoje'] = $stmt->fetch()['total'];
            
            return $stats;
        } catch(PDOException $e) {
            error_log("Erro ao buscar estatísticas de candidaturas: " . $e->getMessage());
            return false;
        }
    }

    public function getRecent($limit = 10) {
        try {
            $stmt = $this->conn->prepare("
                SELECT c.*, v.titulo as vaga_titulo, u.nome as candidato_nome,
                       e.razao_social as empresa_nome
                FROM candidaturas c
                JOIN vagas v ON c.vaga_id = v.id
                JOIN candidatos cand ON c.candidato_id = cand.id
                JOIN usuarios u ON cand.usuario_id = u.id
                JOIN empresas e ON v.empresa_id = e.id
                ORDER BY c.data_candidatura DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Erro ao buscar candidaturas recentes: " . $e->getMessage());
            return [];
        }
    }
}
?>