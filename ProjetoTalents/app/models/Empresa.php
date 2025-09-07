<?php
// Arquivo: app/models/Empresa.php
// TalentsHUB - Modelo de Empresa

class Empresa {
    private $conn;

    public function __construct() {
        $this->conn = getDbConnection();
    }

    public function create($usuario_id, $cnpj, $razao_social, $telefone, $endereco, $cidade, $estado, $cep, $site, $linkedin, $descricao, $setor, $porte, $funcionarios) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO empresas (
                    usuario_id, cnpj, razao_social, telefone, endereco, cidade, estado, cep,
                    site, linkedin, descricao, setor, porte, funcionarios, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            return $stmt->execute([
                $usuario_id, $cnpj, $razao_social, $telefone, $endereco, $cidade, $estado, $cep,
                $site, $linkedin, $descricao, $setor, $porte, $funcionarios
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao criar empresa: " . $e->getMessage());
            return false;
        }
    }

    public function findById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT e.*, u.nome, u.email, u.tipo_usuario
                FROM empresas e
                JOIN usuarios u ON e.usuario_id = u.id
                WHERE e.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro ao buscar empresa por ID: " . $e->getMessage());
            return false;
        }
    }

    public function findByUsuarioId($usuario_id) {
        try {
            error_log("Debug - Buscando empresa para usuário ID: $usuario_id");
            
            $stmt = $this->conn->prepare("
                SELECT e.*, u.nome, u.email, u.tipo_usuario
                FROM empresas e
                JOIN usuarios u ON e.usuario_id = u.id
                WHERE e.usuario_id = ?
            ");
            $stmt->execute([$usuario_id]);
            $result = $stmt->fetch();
            
            if ($result) {
                error_log("Debug - Empresa encontrada: ID=" . $result['id'] . ", Razão Social=" . $result['razao_social']);
            } else {
                error_log("Debug - Nenhuma empresa encontrada para usuário ID: $usuario_id");
            }
            
            return $result !== false ? $result : null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar empresa por usuário: " . $e->getMessage());
            return null;
        }
    }

    public function findByCnpj($cnpj) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM empresas WHERE cnpj = ?");
            $stmt->execute([$cnpj]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro ao buscar empresa por CNPJ: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $usuario_id, $cnpj, $razao_social, $telefone, $endereco, $cidade, $estado, $cep, $site, $linkedin, $descricao, $setor, $porte, $funcionarios) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE empresas SET 
                    cnpj = ?, razao_social = ?, telefone = ?, endereco = ?, cidade = ?, 
                    estado = ?, cep = ?, site = ?, linkedin = ?, descricao = ?, 
                    setor = ?, porte = ?, funcionarios = ?, updated_at = NOW()
                WHERE id = ? AND usuario_id = ?
            ");
            
            return $stmt->execute([
                $cnpj, $razao_social, $telefone, $endereco, $cidade, $estado, $cep,
                $site, $linkedin, $descricao, $setor, $porte, $funcionarios, $id, $usuario_id
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar empresa: " . $e->getMessage());
            return false;
        }
    }

    public function getAll($filters = [], $limit = 20, $offset = 0) {
        try {
            $where = "WHERE 1=1";
            $params = [];
            
            // Aplicar filtros
            if (!empty($filters['setor'])) {
                $where .= " AND e.setor = ?";
                $params[] = $filters['setor'];
            }
            
            if (!empty($filters['porte'])) {
                $where .= " AND e.porte = ?";
                $params[] = $filters['porte'];
            }
            
            if (!empty($filters['cidade'])) {
                $where .= " AND e.cidade LIKE ?";
                $params[] = "%" . $filters['cidade'] . "%";
            }
            
            if (!empty($filters['search'])) {
                $where .= " AND (e.razao_social LIKE ? OR e.descricao LIKE ?)";
                $search = "%" . $filters['search'] . "%";
                $params[] = $search;
                $params[] = $search;
            }
            
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->conn->prepare("
                SELECT e.*, u.nome, u.email,
                       (SELECT COUNT(*) FROM vagas v WHERE v.empresa_id = e.id AND v.status = 'ativa') as total_vagas,
                       e.setor as ramo_atividade,
                       e.porte as tamanho_empresa
                FROM empresas e
                JOIN usuarios u ON e.usuario_id = u.id
                $where
                ORDER BY e.created_at DESC
                LIMIT ? OFFSET ?
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar empresas: " . $e->getMessage());
            return false;
        }
    }

    public function getStats($empresa_id = null) {
        try {
            $stats = [];
            $where = $empresa_id ? "WHERE e.id = ?" : "";
            $params = $empresa_id ? [$empresa_id] : [];
            
            // Total de empresas
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM empresas e $where");
            $stmt->execute($params);
            $stats['total_empresas'] = $stmt->fetch()['total'];
            
            // Empresas por setor
            $stmt = $this->conn->prepare("
                SELECT setor, COUNT(*) as total 
                FROM empresas e $where
                GROUP BY setor 
                ORDER BY total DESC 
                LIMIT 10
            ");
            $stmt->execute($params);
            $stats['por_setor'] = $stmt->fetchAll();
            
            // Empresas por porte
            $stmt = $this->conn->prepare("
                SELECT porte, COUNT(*) as total 
                FROM empresas e $where
                GROUP BY porte 
                ORDER BY total DESC
            ");
            $stmt->execute($params);
            $stats['por_porte'] = $stmt->fetchAll();
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Erro ao buscar estatísticas de empresas: " . $e->getMessage());
            return false;
        }
    }

    public function getSetores() {
        try {
            $stmt = $this->conn->prepare("
                SELECT DISTINCT setor 
                FROM empresas 
                WHERE setor IS NOT NULL AND setor != '' 
                ORDER BY setor
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar setores: " . $e->getMessage());
            return false;
        }
    }

    public function getTopEmpresas($limit = 10) {
        try {
            $stmt = $this->conn->prepare("
                SELECT e.*, u.nome,
                       (SELECT COUNT(*) FROM vagas v WHERE v.empresa_id = e.id AND v.status = 'ativa') as vagas_ativas,
                       (SELECT COUNT(*) FROM candidaturas c 
                        JOIN vagas v ON c.vaga_id = v.id 
                        WHERE v.empresa_id = e.id) as total_candidaturas
                FROM empresas e
                JOIN usuarios u ON e.usuario_id = u.id
                ORDER BY vagas_ativas DESC, total_candidaturas DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar top empresas: " . $e->getMessage());
            return false;
        }
    }
}
?>

