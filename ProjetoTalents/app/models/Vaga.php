<?php
// Arquivo: app/models/Vaga.php
// TalentsHUB - Modelo de Vaga

class Vaga {
    private $conn;

    public function __construct() {
        $this->conn = getDbConnection();
    }

    public function create($empresa_id, $titulo, $descricao, $requisitos, $beneficios, $salario_min, $salario_max, $tipo_contrato, $modalidade, $nivel_experiencia, $area, $localizacao, $data_limite) {
        try {
            error_log("Debug - Criando vaga para empresa ID: $empresa_id, Título: $titulo");
            
            $stmt = $this->conn->prepare("
                INSERT INTO vagas (
                    empresa_id, titulo, descricao, requisitos, beneficios, 
                    salario_min, salario_max, tipo_contrato, modalidade, 
                    nivel_experiencia, area, localizacao, data_limite, 
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $result = $stmt->execute([
                $empresa_id, $titulo, $descricao, $requisitos, $beneficios,
                $salario_min, $salario_max, $tipo_contrato, $modalidade,
                $nivel_experiencia, $area, $localizacao, $data_limite
            ]);
            
            error_log("Debug - Resultado do execute: " . ($result ? 'SUCESSO' : 'FALHA'));
            if ($result) {
                error_log("Debug - Vaga criada com ID: " . $this->conn->lastInsertId());
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Erro ao criar vaga: " . $e->getMessage());
            return false;
        }
    }

    public function findById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT v.*, e.razao_social, e.descricao as empresa_descricao, 
                       u.nome as empresa_nome, u.email as empresa_email
                FROM vagas v
                JOIN empresas e ON v.empresa_id = e.id
                JOIN usuarios u ON e.usuario_id = u.id
                WHERE v.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro ao buscar vaga por ID: " . $e->getMessage());
            return false;
        }
    }

    public function findActiveById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT v.*, e.razao_social, e.descricao as empresa_descricao, 
                       u.nome as empresa_nome, u.email as empresa_email
                FROM vagas v
                JOIN empresas e ON v.empresa_id = e.id
                JOIN usuarios u ON e.usuario_id = u.id
                WHERE v.id = ? AND v.status = 'ativa'
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro ao buscar vaga ativa por ID: " . $e->getMessage());
            return false;
        }
    }

    public function getAll($limit = 20, $offset = 0, $filters = []) {
        try {
            $where = "WHERE v.status = 'ativa'";
            $params = [];
            
            // Aplicar filtros
            if (!empty($filters['area'])) {
                $where .= " AND v.area = ?";
                $params[] = $filters['area'];
            }
            
            if (!empty($filters['modalidade'])) {
                $where .= " AND v.modalidade = ?";
                $params[] = $filters['modalidade'];
            }
            
            if (!empty($filters['nivel_experiencia'])) {
                $where .= " AND v.nivel_experiencia = ?";
                $params[] = $filters['nivel_experiencia'];
            }
            
            if (!empty($filters['tipo_contrato'])) {
                $where .= " AND v.tipo_contrato = ?";
                $params[] = $filters['tipo_contrato'];
            }
            
            if (!empty($filters['localizacao'])) {
                $where .= " AND v.localizacao LIKE ?";
                $params[] = "%" . $filters['localizacao'] . "%";
            }
            
            if (!empty($filters['search'])) {
                $where .= " AND (v.titulo LIKE ? OR v.descricao LIKE ? OR v.requisitos LIKE ?)";
                $search = "%" . $filters['search'] . "%";
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
            }
            
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->conn->prepare("
                SELECT v.*, e.razao_social, u.nome as empresa_nome
                FROM vagas v
                JOIN empresas e ON v.empresa_id = e.id
                JOIN usuarios u ON e.usuario_id = u.id
                $where
                ORDER BY v.created_at DESC
                LIMIT ? OFFSET ?
            ");
            
            $stmt->execute($params);
            $result = $stmt->fetchAll();
            return $result !== false ? $result : [];
        } catch (PDOException $e) {
            error_log("Erro ao buscar vagas: " . $e->getMessage());
            return [];
        }
    }

    public function getByEmpresa($empresa_id, $limit = null, $offset = 0) {
        try {
            error_log("Debug - Buscando vagas para empresa ID: $empresa_id");
            
            // Primeiro, verificar se a empresa existe
            $stmt_check = $this->conn->prepare("SELECT id FROM empresas WHERE id = ?");
            $stmt_check->execute([$empresa_id]);
            $empresa_exists = $stmt_check->fetch();
            
            if (!$empresa_exists) {
                error_log("Debug - Empresa com ID $empresa_id não existe");
                return [];
            }
            
            // Buscar vagas - versão simplificada primeiro
            if ($limit !== null) {
                $stmt = $this->conn->prepare("
                    SELECT v.*, 0 as total_candidaturas
                    FROM vagas v
                    WHERE v.empresa_id = ?
                    ORDER BY v.created_at DESC
                    LIMIT ? OFFSET ?
                ");
                $stmt->execute([$empresa_id, $limit, $offset]);
            } else {
                $stmt = $this->conn->prepare("
                    SELECT v.*, 0 as total_candidaturas
                    FROM vagas v
                    WHERE v.empresa_id = ?
                    ORDER BY v.created_at DESC
                ");
                $stmt->execute([$empresa_id]);
            }
            $result = $stmt->fetchAll();
            
            error_log("Debug - Empresa ID: $empresa_id, Vagas encontradas: " . count($result));
            
            // Log detalhado das vagas encontradas
            foreach ($result as $vaga) {
                error_log("Debug - Vaga encontrada: ID=" . $vaga['id'] . ", Título=" . $vaga['titulo'] . ", Status=" . $vaga['status']);
            }
            
            return $result !== false ? $result : [];
        } catch (PDOException $e) {
            error_log("Erro ao buscar vagas da empresa: " . $e->getMessage());
            return [];
        }
    }

    public function update($id, $empresa_id, $titulo, $descricao, $requisitos, $beneficios, $salario_min, $salario_max, $tipo_contrato, $modalidade, $nivel_experiencia, $area, $localizacao, $data_limite, $status) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE vagas SET 
                    titulo = ?, descricao = ?, requisitos = ?, beneficios = ?,
                    salario_min = ?, salario_max = ?, tipo_contrato = ?, modalidade = ?,
                    nivel_experiencia = ?, area = ?, localizacao = ?, data_limite = ?,
                    status = ?, updated_at = NOW()
                WHERE id = ? AND empresa_id = ?
            ");
            
            return $stmt->execute([
                $titulo, $descricao, $requisitos, $beneficios,
                $salario_min, $salario_max, $tipo_contrato, $modalidade,
                $nivel_experiencia, $area, $localizacao, $data_limite,
                $status, $id, $empresa_id
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar vaga: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id, $empresa_id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM vagas WHERE id = ? AND empresa_id = ?");
            return $stmt->execute([$id, $empresa_id]);
        } catch (PDOException $e) {
            error_log("Erro ao deletar vaga: " . $e->getMessage());
            return false;
        }
    }

    public function incrementViews($id) {
        try {
            $stmt = $this->conn->prepare("UPDATE vagas SET visualizacoes = visualizacoes + 1 WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Erro ao incrementar visualizações: " . $e->getMessage());
            return false;
        }
    }

    public function getStats($empresa_id = null) {
        try {
            $stats = [];
            $where = $empresa_id ? "WHERE empresa_id = ?" : "";
            $params = $empresa_id ? [$empresa_id] : [];
            
            // Total de vagas
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM vagas $where");
            $stmt->execute($params);
            $stats['total_vagas'] = $stmt->fetch()['total'];
            
            // Vagas ativas
            $where_active = $empresa_id ? "WHERE empresa_id = ? AND status = 'ativa'" : "WHERE status = 'ativa'";
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM vagas $where_active");
            $stmt->execute($params);
            $stats['vagas_ativas'] = $stmt->fetch()['total'];
            
            // Total de visualizações
            $stmt = $this->conn->prepare("SELECT SUM(visualizacoes) as total FROM vagas $where");
            $stmt->execute($params);
            $stats['total_visualizacoes'] = $stmt->fetch()['total'] ?? 0;
            
            // Vagas por área
            $stmt = $this->conn->prepare("
                SELECT area, COUNT(*) as total 
                FROM vagas $where 
                GROUP BY area 
                ORDER BY total DESC 
                LIMIT 10
            ");
            $stmt->execute($params);
            $stats['vagas_por_area'] = $stmt->fetchAll();
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Erro ao buscar estatísticas de vagas: " . $e->getMessage());
            return false;
        }
    }

    public function getAreas() {
        try {
            $stmt = $this->conn->prepare("
                SELECT DISTINCT area 
                FROM vagas 
                WHERE area IS NOT NULL AND area != '' 
                ORDER BY area
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar áreas: " . $e->getMessage());
            return false;
        }
    }

    public function getRecent($limit = 5) {
        try {
            $stmt = $this->conn->prepare("
                SELECT v.*, e.razao_social, u.nome as empresa_nome
                FROM vagas v
                JOIN empresas e ON v.empresa_id = e.id
                JOIN usuarios u ON e.usuario_id = u.id
                WHERE v.status = 'ativa'
                ORDER BY v.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            $result = $stmt->fetchAll();
            return $result !== false ? $result : [];
        } catch (PDOException $e) {
            error_log("Erro ao buscar vagas recentes: " . $e->getMessage());
            return [];
        }
    }

    public function getFeatured($limit = 5) {
        try {
            $stmt = $this->conn->prepare("
                SELECT v.*, e.razao_social, u.nome as empresa_nome
                FROM vagas v
                JOIN empresas e ON v.empresa_id = e.id
                JOIN usuarios u ON e.usuario_id = u.id
                WHERE v.status = 'ativa'
                ORDER BY v.visualizacoes DESC, v.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar vagas em destaque: " . $e->getMessage());
            return false;
        }
    }

    public function getDemandaPorRegiao($regiao = null) {
        try {
            $where = "WHERE v.status = 'ativa'";
            $params = [];
            
            if (!empty($regiao)) {
                $where .= " AND v.localizacao LIKE ?";
                $params[] = "%" . $regiao . "%";
            }
            
            $stmt = $this->conn->prepare("
                SELECT 
                    v.area,
                    v.localizacao,
                    COUNT(*) as total_vagas,
                    AVG(v.salario_min) as salario_medio_min,
                    AVG(v.salario_max) as salario_medio_max,
                    COUNT(DISTINCT v.empresa_id) as total_empresas
                FROM vagas v
                $where
                GROUP BY v.area, v.localizacao
                ORDER BY total_vagas DESC, v.area
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar demanda por região: " . $e->getMessage());
            return false;
        }
    }

    public function getTopAreasPorRegiao($regiao = null, $limit = 10) {
        try {
            $where = "WHERE v.status = 'ativa'";
            $params = [];
            
            if (!empty($regiao)) {
                $where .= " AND v.localizacao LIKE ?";
                $params[] = "%" . $regiao . "%";
            }
            
            $params[] = $limit;
            
            $stmt = $this->conn->prepare("
                SELECT 
                    v.area,
                    COUNT(*) as total_vagas,
                    COUNT(DISTINCT v.empresa_id) as total_empresas,
                    AVG(v.salario_min) as salario_medio_min,
                    AVG(v.salario_max) as salario_medio_max,
                    MAX(v.created_at) as ultima_vaga
                FROM vagas v
                $where
                GROUP BY v.area
                ORDER BY total_vagas DESC
                LIMIT ?
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar top áreas por região: " . $e->getMessage());
            return false;
        }
    }

    public function getRegioesDisponiveis() {
        try {
            $stmt = $this->conn->prepare("
                SELECT DISTINCT 
                    CASE 
                        WHEN localizacao LIKE '%São Paulo%' THEN 'São Paulo'
                        WHEN localizacao LIKE '%Rio de Janeiro%' THEN 'Rio de Janeiro'
                        WHEN localizacao LIKE '%Belo Horizonte%' THEN 'Belo Horizonte'
                        WHEN localizacao LIKE '%Brasília%' THEN 'Brasília'
                        WHEN localizacao LIKE '%Salvador%' THEN 'Salvador'
                        WHEN localizacao LIKE '%Fortaleza%' THEN 'Fortaleza'
                        WHEN localizacao LIKE '%Recife%' THEN 'Recife'
                        WHEN localizacao LIKE '%Porto Alegre%' THEN 'Porto Alegre'
                        WHEN localizacao LIKE '%Curitiba%' THEN 'Curitiba'
                        WHEN localizacao LIKE '%Goiânia%' THEN 'Goiânia'
                        WHEN localizacao LIKE '%Remoto%' THEN 'Remoto'
                        ELSE 'Outras Regiões'
                    END as regiao,
                    COUNT(*) as total_vagas
                FROM vagas 
                WHERE status = 'ativa'
                GROUP BY regiao
                ORDER BY total_vagas DESC
            ");
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar regiões disponíveis: " . $e->getMessage());
            return false;
        }
    }

    public function getLocalizacoesDisponiveis($termo = '') {
        try {
            $where = "WHERE status = 'ativa'";
            $params = [];
            
            if (!empty($termo)) {
                $where .= " AND localizacao LIKE ?";
                $params[] = "%" . $termo . "%";
            }
            
            $stmt = $this->conn->prepare("
                SELECT DISTINCT localizacao, COUNT(*) as total_vagas
                FROM vagas 
                $where
                GROUP BY localizacao
                ORDER BY total_vagas DESC, localizacao
                LIMIT 20
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar localizações disponíveis: " . $e->getMessage());
            return false;
        }
    }

    public function getAreasDisponiveis($termo = '') {
        try {
            $where = "WHERE status = 'ativa' AND area IS NOT NULL AND area != ''";
            $params = [];
            
            if (!empty($termo)) {
                $where .= " AND area LIKE ?";
                $params[] = "%" . $termo . "%";
            }
            
            $stmt = $this->conn->prepare("
                SELECT DISTINCT area, COUNT(*) as total_vagas
                FROM vagas 
                $where
                GROUP BY area
                ORDER BY total_vagas DESC, area
                LIMIT 20
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar áreas disponíveis: " . $e->getMessage());
            return false;
        }
    }

    public function getVagasComEndereco($limit = 20) {
        try {
            $stmt = $this->conn->prepare("
                SELECT v.id, v.titulo, v.localizacao, e.razao_social, e.endereco
                FROM vagas v
                JOIN empresas e ON v.empresa_id = e.id
                WHERE v.status = 'ativa' 
                AND e.endereco IS NOT NULL 
                AND e.endereco != ''
                ORDER BY v.created_at DESC
                LIMIT ?
            ");
            
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar vagas com endereço: " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorCidade($cidade) {
        try {
            $stmt = $this->conn->prepare("
                SELECT DISTINCT localizacao, COUNT(*) as total_vagas
                FROM vagas 
                WHERE status = 'ativa' AND localizacao LIKE ?
                GROUP BY localizacao
                ORDER BY total_vagas DESC
                LIMIT 10
            ");
            
            $stmt->execute(["%" . $cidade . "%"]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar por cidade: " . $e->getMessage());
            return false;
        }
    }

    public function getTopAreasPorCidade($cidade, $limit = 15, $filtros = []) {
        try {
            $where = "WHERE v.status = 'ativa' AND v.localizacao LIKE ?";
            $params = ["%" . $cidade . "%"];
            
            // Aplicar filtros adicionais
            if (!empty($filtros['area'])) {
                $where .= " AND v.area = ?";
                $params[] = $filtros['area'];
            }
            
            if (!empty($filtros['modalidade'])) {
                $where .= " AND v.modalidade = ?";
                $params[] = $filtros['modalidade'];
            }
            
            if (!empty($filtros['nivel_experiencia'])) {
                $where .= " AND v.nivel_experiencia = ?";
                $params[] = $filtros['nivel_experiencia'];
            }
            
            if (!empty($filtros['tipo_contrato'])) {
                $where .= " AND v.tipo_contrato = ?";
                $params[] = $filtros['tipo_contrato'];
            }
            
            $params[] = $limit;
            
            $stmt = $this->conn->prepare("
                SELECT 
                    v.area,
                    v.localizacao,
                    COUNT(*) as total_vagas,
                    COUNT(DISTINCT v.empresa_id) as total_empresas,
                    AVG(v.salario_min) as salario_medio_min,
                    AVG(v.salario_max) as salario_medio_max,
                    MAX(v.created_at) as ultima_vaga
                FROM vagas v
                $where
                GROUP BY v.area, v.localizacao
                ORDER BY total_vagas DESC
                LIMIT ?
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar top áreas por cidade: " . $e->getMessage());
            return false;
        }
    }

    public function getTopAreasPorRegiaoComFiltros($regiao = null, $limit = 15, $filtros = []) {
        try {
            $where = "WHERE v.status = 'ativa'";
            $params = [];
            
            if (!empty($regiao)) {
                $where .= " AND v.localizacao LIKE ?";
                $params[] = "%" . $regiao . "%";
            }
            
            // Aplicar filtros adicionais
            if (!empty($filtros['area'])) {
                $where .= " AND v.area = ?";
                $params[] = $filtros['area'];
            }
            
            if (!empty($filtros['modalidade'])) {
                $where .= " AND v.modalidade = ?";
                $params[] = $filtros['modalidade'];
            }
            
            if (!empty($filtros['nivel_experiencia'])) {
                $where .= " AND v.nivel_experiencia = ?";
                $params[] = $filtros['nivel_experiencia'];
            }
            
            if (!empty($filtros['tipo_contrato'])) {
                $where .= " AND v.tipo_contrato = ?";
                $params[] = $filtros['tipo_contrato'];
            }
            
            $params[] = $limit;
            
            $stmt = $this->conn->prepare("
                SELECT 
                    v.area,
                    COUNT(*) as total_vagas,
                    COUNT(DISTINCT v.empresa_id) as total_empresas,
                    AVG(v.salario_min) as salario_medio_min,
                    AVG(v.salario_max) as salario_medio_max,
                    MAX(v.created_at) as ultima_vaga
                FROM vagas v
                $where
                GROUP BY v.area
                ORDER BY total_vagas DESC
                LIMIT ?
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar top áreas por região com filtros: " . $e->getMessage());
            return false;
        }
    }
}
?>