<?php
// Arquivo: app/controllers/SearchController.php
// TalentsHUB - Controller de Busca

require_once __DIR__ . '/../utils/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'vagas':
            searchVagas();
            break;
        case 'candidatos':
            searchCandidatos();
            break;
        case 'empresas':
            searchEmpresas();
            break;
        case 'suggestions':
            getSuggestions();
            break;
        default:
            searchVagas();
    }
} else {
    header('Location: ' . BASE_DIR . '/app/views/vagas.php');
    exit();
}

function searchVagas() {
    $vagaModel = new Vaga();
    
    // Parâmetros de busca
    $filters = [
        'search' => trim($_GET['search'] ?? ''),
        'area' => $_GET['area'] ?? '',
        'modalidade' => $_GET['modalidade'] ?? '',
        'nivel_experiencia' => $_GET['nivel_experiencia'] ?? '',
        'tipo_contrato' => $_GET['tipo_contrato'] ?? '',
        'localizacao' => trim($_GET['localizacao'] ?? ''),
        'salario_min' => !empty($_GET['salario_min']) ? floatval($_GET['salario_min']) : null,
        'salario_max' => !empty($_GET['salario_max']) ? floatval($_GET['salario_max']) : null
    ];
    
    // Paginação
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 20;
    $offset = ($page - 1) * $limit;
    
    // Buscar vagas
    $vagas = $vagaModel->getAll($limit, $offset, $filters);
    $total_vagas_result = $vagaModel->getAll(1000, 0, $filters);
    $total_vagas = is_array($total_vagas_result) ? count($total_vagas_result) : 0;
    
    // Buscar áreas para filtros
    $areas = $vagaModel->getAreas();
    
    // Preparar dados para a view
    $data = [
        'vagas' => $vagas,
        'areas' => $areas,
        'filters' => $filters,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => ceil($total_vagas / $limit),
            'total_items' => $total_vagas,
            'limit' => $limit
        ]
    ];
    
    // Incluir a view
    include __DIR__ . '/../views/vagas.php';
}

function searchCandidatos() {
    // Verificar se é empresa ou admin
    if (!in_array($_SESSION['usuario_tipo'] ?? '', ['empresa', 'admin'])) {
        $_SESSION['search_erro'] = "Apenas empresas podem buscar candidatos.";
        header('Location: ' . BASE_DIR . '/app/views/vagas.php');
        exit();
    }
    
    $candidatoModel = new Candidato();
    
    // Parâmetros de busca
    $filters = [
        'search' => trim($_GET['search'] ?? ''),
        'cidade' => trim($_GET['cidade'] ?? ''),
        'estado' => $_GET['estado'] ?? '',
        'disponibilidade' => $_GET['disponibilidade'] ?? ''
    ];
    
    // Paginação
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 20;
    $offset = ($page - 1) * $limit;
    
    // Buscar candidatos
    $candidatos = $candidatoModel->getAll($limit, $offset, $filters);
    $total_candidatos_result = $candidatoModel->getAll(1000, 0, $filters);
    $total_candidatos = is_array($total_candidatos_result) ? count($total_candidatos_result) : 0;
    
    // Preparar dados para a view
    $data = [
        'candidatos' => $candidatos,
        'filters' => $filters,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => ceil($total_candidatos / $limit),
            'total_items' => $total_candidatos,
            'limit' => $limit
        ]
    ];
    
    // Incluir a view
    include __DIR__ . '/../views/buscar_candidatos.php';
}

function searchEmpresas() {
    $empresaModel = new Empresa();
    
    // Parâmetros de busca
    $filters = [
        'search' => trim($_GET['search'] ?? ''),
        'setor' => $_GET['setor'] ?? '',
        'porte' => $_GET['porte'] ?? '',
        'cidade' => trim($_GET['cidade'] ?? '')
    ];
    
    // Paginação
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 20;
    $offset = ($page - 1) * $limit;
    
    // Buscar empresas
    $empresas = $empresaModel->getAll($limit, $offset, $filters);
    $total_empresas_result = $empresaModel->getAll(1000, 0, $filters);
    $total_empresas = is_array($total_empresas_result) ? count($total_empresas_result) : 0;
    
    // Buscar setores para filtros
    $setores = $empresaModel->getSetores();
    
    // Preparar dados para a view
    $data = [
        'empresas' => $empresas,
        'setores' => $setores,
        'filters' => $filters,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => ceil($total_empresas / $limit),
            'total_items' => $total_empresas,
            'limit' => $limit
        ]
    ];
    
    // Incluir a view
    include __DIR__ . '/../views/buscar_empresas.php';
}

function getSuggestions() {
    $type = $_GET['type'] ?? '';
    $query = trim($_GET['q'] ?? '');
    
    if (empty($query) || strlen($query) < 2) {
        echo json_encode([]);
        exit();
    }
    
    $suggestions = [];
    
    switch ($type) {
        case 'areas':
            $vagaModel = new Vaga();
            $areas = $vagaModel->getAreas();
            foreach ($areas as $area) {
                if (stripos($area['area'], $query) !== false) {
                    $suggestions[] = $area['area'];
                }
            }
            break;
            
        case 'setores':
            $empresaModel = new Empresa();
            $setores = $empresaModel->getSetores();
            foreach ($setores as $setor) {
                if (stripos($setor['setor'], $query) !== false) {
                    $suggestions[] = $setor['setor'];
                }
            }
            break;
            
        case 'cidades':
            $candidatoModel = new Candidato();
            $empresaModel = new Empresa();
            
            // Buscar cidades de candidatos
            $conn = getDbConnection();
            $stmt = $conn->prepare("
                SELECT DISTINCT cidade 
                FROM candidatos 
                WHERE cidade LIKE ? AND cidade IS NOT NULL AND cidade != ''
                LIMIT 10
            ");
            $stmt->execute(["%$query%"]);
            $cidades_candidatos = $stmt->fetchAll();
            
            // Buscar cidades de empresas
            $stmt = $conn->prepare("
                SELECT DISTINCT cidade 
                FROM empresas 
                WHERE cidade LIKE ? AND cidade IS NOT NULL AND cidade != ''
                LIMIT 10
            ");
            $stmt->execute(["%$query%"]);
            $cidades_empresas = $stmt->fetchAll();
            
            $cidades = array_merge($cidades_candidatos, $cidades_empresas);
            $cidades_unicas = array_unique(array_column($cidades, 'cidade'));
            
            foreach ($cidades_unicas as $cidade) {
                $suggestions[] = $cidade;
            }
            break;
    }
    
    // Limitar a 10 sugestões
    $suggestions = array_slice($suggestions, 0, 10);
    
    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit();
}

// Função auxiliar para gerar URL de busca
function buildSearchUrl($filters, $page = 1) {
    $params = array_filter($filters, function($value) {
        return !empty($value);
    });
    $params['page'] = $page;
    return BASE_DIR . '/app/controllers/SearchController.php?' . http_build_query($params);
}

// Função auxiliar para gerar URL de paginação
function buildPaginationUrl($filters, $page) {
    return buildSearchUrl($filters, $page);
}
?>

