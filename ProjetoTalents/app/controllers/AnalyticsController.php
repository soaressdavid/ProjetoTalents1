<?php
// Arquivo: app/controllers/AnalyticsController.php
// TalentsHUB - Controller de Analytics

require_once __DIR__ . '/../utils/init.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_DIR . '/app/views/auth.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'dashboard':
            showDashboard();
            break;
        case 'vagas':
            showVagasAnalytics();
            break;
        case 'candidaturas':
            showCandidaturasAnalytics();
            break;
        case 'empresas':
            showEmpresasAnalytics();
            break;
        case 'candidatos':
            showCandidatosAnalytics();
            break;
        case 'export':
            exportData();
            break;
        default:
            showDashboard();
    }
} else {
    header('Location: ' . BASE_DIR . '/app/views/dashboard.php');
    exit();
}

function showDashboard() {
    $userModel = new User();
    $vagaModel = new Vaga();
    $candidaturaModel = new Candidatura();
    $empresaModel = new Empresa();
    $candidatoModel = new Candidato();
    
    $stats = [];
    
    // Estatísticas gerais
    $stats['usuarios'] = $userModel->getStats();
    $stats['vagas'] = $vagaModel->getStats();
    $stats['candidaturas'] = $candidaturaModel->getStats();
    $stats['empresas'] = $empresaModel->getStats();
    $stats['candidatos'] = $candidatoModel->getStats();
    
    // Dados específicos por tipo de usuário
    if ($_SESSION['usuario_tipo'] === 'empresa') {
        $empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);
        if ($empresa) {
            $stats['minhas_vagas'] = $vagaModel->getStats($empresa['id']);
            $stats['minhas_candidaturas'] = $candidaturaModel->getStats($empresa['id']);
        }
    }
    
    // Vagas recentes
    $stats['vagas_recentes'] = $vagaModel->getRecent(5);
    $stats['vagas_destaque'] = $vagaModel->getFeatured(5);
    
    // Candidaturas recentes
    $stats['candidaturas_recentes'] = $candidaturaModel->getRecent(10);
    
    // Top empresas
    $stats['top_empresas'] = $empresaModel->getTopEmpresas(10);
    
    // Incluir a view
    include __DIR__ . '/../views/dashboard.php';
}

function showVagasAnalytics() {
    // Verificar permissões
    if (!in_array($_SESSION['usuario_tipo'], ['empresa', 'admin'])) {
        header('Location: ' . BASE_DIR . '/app/views/dashboard.php');
        exit();
    }
    
    $vagaModel = new Vaga();
    $empresaModel = new Empresa();
    
    $empresa_id = null;
    if ($_SESSION['usuario_tipo'] === 'empresa') {
        $empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);
        $empresa_id = $empresa ? $empresa['id'] : null;
    }
    
    // Período de análise
    $periodo = $_GET['periodo'] ?? '30'; // dias
    $data_inicio = date('Y-m-d', strtotime("-$periodo days"));
    
    // Estatísticas de vagas
    $stats = $vagaModel->getStats($empresa_id);
    
    // Vagas por período
    $stmt = $vagaModel->conn->prepare("
        SELECT DATE(created_at) as data, COUNT(*) as total
        FROM vagas 
        WHERE created_at >= ? " . ($empresa_id ? "AND empresa_id = ?" : "") . "
        GROUP BY DATE(created_at)
        ORDER BY data DESC
    ");
    $params = [$data_inicio];
    if ($empresa_id) $params[] = $empresa_id;
    $stmt->execute($params);
    $vagas_periodo = $stmt->fetchAll();
    
    // Visualizações por período
    $stmt = $vagaModel->conn->prepare("
        SELECT DATE(created_at) as data, SUM(visualizacoes) as total
        FROM vagas 
        WHERE created_at >= ? " . ($empresa_id ? "AND empresa_id = ?" : "") . "
        GROUP BY DATE(created_at)
        ORDER BY data DESC
    ");
    $stmt->execute($params);
    $visualizacoes_periodo = $stmt->fetchAll();
    
    $data = [
        'stats' => $stats,
        'vagas_periodo' => $vagas_periodo,
        'visualizacoes_periodo' => $visualizacoes_periodo,
        'periodo' => $periodo,
        'empresa_id' => $empresa_id
    ];
    
    include __DIR__ . '/../views/analytics_vagas.php';
}

function showCandidaturasAnalytics() {
    // Verificar permissões
    if (!in_array($_SESSION['usuario_tipo'], ['empresa', 'admin'])) {
        header('Location: ' . BASE_DIR . '/app/views/dashboard.php');
        exit();
    }
    
    $candidaturaModel = new Candidatura();
    $empresaModel = new Empresa();
    
    $empresa_id = null;
    if ($_SESSION['usuario_tipo'] === 'empresa') {
        $empresa = $empresaModel->findByUsuarioId($_SESSION['usuario_id']);
        $empresa_id = $empresa ? $empresa['id'] : null;
    }
    
    // Período de análise
    $periodo = $_GET['periodo'] ?? '30'; // dias
    $data_inicio = date('Y-m-d', strtotime("-$periodo days"));
    
    // Estatísticas de candidaturas
    $stats = $candidaturaModel->getStats($empresa_id);
    
    // Candidaturas por período
    $where = $empresa_id ? "WHERE v.empresa_id = ? AND c.data_candidatura >= ?" : "WHERE c.data_candidatura >= ?";
    $params = $empresa_id ? [$empresa_id, $data_inicio] : [$data_inicio];
    
    $stmt = $candidaturaModel->conn->prepare("
        SELECT DATE(c.data_candidatura) as data, COUNT(*) as total
        FROM candidaturas c
        JOIN vagas v ON c.vaga_id = v.id
        $where
        GROUP BY DATE(c.data_candidatura)
        ORDER BY data DESC
    ");
    $stmt->execute($params);
    $candidaturas_periodo = $stmt->fetchAll();
    
    // Candidaturas por status
    $stmt = $candidaturaModel->conn->prepare("
        SELECT c.status, COUNT(*) as total
        FROM candidaturas c
        JOIN vagas v ON c.vaga_id = v.id
        $where
        GROUP BY c.status
        ORDER BY total DESC
    ");
    $stmt->execute($params);
    $candidaturas_status = $stmt->fetchAll();
    
    $data = [
        'stats' => $stats,
        'candidaturas_periodo' => $candidaturas_periodo,
        'candidaturas_status' => $candidaturas_status,
        'periodo' => $periodo,
        'empresa_id' => $empresa_id
    ];
    
    include __DIR__ . '/../views/analytics_candidaturas.php';
}

function showEmpresasAnalytics() {
    // Verificar se é admin
    if ($_SESSION['usuario_tipo'] !== 'admin') {
        header('Location: ' . BASE_DIR . '/app/views/dashboard.php');
        exit();
    }
    
    $empresaModel = new Empresa();
    $vagaModel = new Vaga();
    $candidaturaModel = new Candidatura();
    
    // Estatísticas gerais
    $stats = $empresaModel->getStats();
    
    // Empresas mais ativas
    $top_empresas = $empresaModel->getTopEmpresas(20);
    
    // Empresas por período de cadastro
    $periodo = $_GET['periodo'] ?? '365'; // dias
    $data_inicio = date('Y-m-d', strtotime("-$periodo days"));
    
    $stmt = $empresaModel->conn->prepare("
        SELECT DATE(created_at) as data, COUNT(*) as total
        FROM empresas 
        WHERE created_at >= ?
        GROUP BY DATE(created_at)
        ORDER BY data DESC
    ");
    $stmt->execute([$data_inicio]);
    $empresas_periodo = $stmt->fetchAll();
    
    $data = [
        'stats' => $stats,
        'top_empresas' => $top_empresas,
        'empresas_periodo' => $empresas_periodo,
        'periodo' => $periodo
    ];
    
    include __DIR__ . '/../views/analytics_empresas.php';
}

function showCandidatosAnalytics() {
    // Verificar se é admin
    if ($_SESSION['usuario_tipo'] !== 'admin') {
        header('Location: ' . BASE_DIR . '/app/views/dashboard.php');
        exit();
    }
    
    $candidatoModel = new Candidato();
    
    // Estatísticas gerais
    $stats = $candidatoModel->getStats();
    
    // Candidatos por período de cadastro
    $periodo = $_GET['periodo'] ?? '365'; // dias
    $data_inicio = date('Y-m-d', strtotime("-$periodo days"));
    
    $stmt = $candidatoModel->conn->prepare("
        SELECT DATE(created_at) as data, COUNT(*) as total
        FROM candidatos 
        WHERE created_at >= ?
        GROUP BY DATE(created_at)
        ORDER BY data DESC
    ");
    $stmt->execute([$data_inicio]);
    $candidatos_periodo = $stmt->fetchAll();
    
    $data = [
        'stats' => $stats,
        'candidatos_periodo' => $candidatos_periodo,
        'periodo' => $periodo
    ];
    
    include __DIR__ . '/../views/analytics_candidatos.php';
}

function exportData() {
    // Verificar permissões
    if (!in_array($_SESSION['usuario_tipo'], ['empresa', 'admin'])) {
        header('Location: ' . BASE_DIR . '/app/views/dashboard.php');
        exit();
    }
    
    $type = $_GET['type'] ?? '';
    $format = $_GET['format'] ?? 'csv';
    
    if (!in_array($type, ['vagas', 'candidaturas', 'candidatos', 'empresas'])) {
        header('Location: ' . BASE_DIR . '/app/views/dashboard.php');
        exit();
    }
    
    if (!in_array($format, ['csv', 'xlsx'])) {
        $format = 'csv';
    }
    
    // Implementar exportação de dados
    // Por enquanto, redirecionar para dashboard
    $_SESSION['export_erro'] = "Funcionalidade de exportação em desenvolvimento.";
    header('Location: ' . BASE_DIR . '/app/views/dashboard.php');
    exit();
}
?>

