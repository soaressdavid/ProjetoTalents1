<?php
// Arquivo: api/index.php
// TalentsHUB - API REST

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../app/utils/init.php';

// Roteamento da API
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/ProjetoTalents/api', '', $path);
$path = trim($path, '/');

$method = $_SERVER['REQUEST_METHOD'];
$segments = explode('/', $path);

// Autenticação básica (em produção, usar JWT ou OAuth)
function authenticate() {
    $headers = getallheaders();
    $auth_header = $headers['Authorization'] ?? '';
    
    if (strpos($auth_header, 'Bearer ') === 0) {
        $token = substr($auth_header, 7);
        // Aqui você validaria o token JWT
        // Por enquanto, vamos usar uma validação simples
        return validateToken($token);
    }
    
    return false;
}

function validateToken($token) {
    // Implementação simples - em produção usar JWT
    if ($token === 'demo_token_123') {
        return ['id' => 1, 'tipo' => 'admin'];
    }
    return false;
}

function sendResponse($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit();
}

function sendError($message, $status_code = 400) {
    sendResponse(['error' => $message], $status_code);
}

// Roteamento
try {
    switch ($segments[0]) {
        case 'vagas':
            handleVagasAPI($method, $segments);
            break;
            
        case 'candidatos':
            handleCandidatosAPI($method, $segments);
            break;
            
        case 'empresas':
            handleEmpresasAPI($method, $segments);
            break;
            
        case 'candidaturas':
            handleCandidaturasAPI($method, $segments);
            break;
            
        case 'stats':
            handleStatsAPI($method, $segments);
            break;
            
        case 'search':
            handleSearchAPI($method, $segments);
            break;
            
        default:
            sendError('Endpoint não encontrado', 404);
    }
} catch (Exception $e) {
    sendError('Erro interno do servidor: ' . $e->getMessage(), 500);
}

function handleVagasAPI($method, $segments) {
    $vagaModel = new Vaga();
    
    switch ($method) {
        case 'GET':
            if (isset($segments[1])) {
                // GET /api/vagas/{id}
                $vaga = $vagaModel->findById($segments[1]);
                if ($vaga) {
                    sendResponse($vaga);
                } else {
                    sendError('Vaga não encontrada', 404);
                }
            } else {
                // GET /api/vagas
                $filters = [
                    'search' => $_GET['search'] ?? '',
                    'area' => $_GET['area'] ?? '',
                    'modalidade' => $_GET['modalidade'] ?? '',
                    'nivel_experiencia' => $_GET['nivel_experiencia'] ?? '',
                    'tipo_contrato' => $_GET['tipo_contrato'] ?? '',
                    'localizacao' => $_GET['localizacao'] ?? ''
                ];
                
                $limit = min(50, intval($_GET['limit'] ?? 20));
                $offset = intval($_GET['offset'] ?? 0);
                
                $vagas = $vagaModel->getAll($limit, $offset, $filters);
                sendResponse(['vagas' => $vagas, 'total' => count($vagas)]);
            }
            break;
            
        case 'POST':
            // Requer autenticação
            $user = authenticate();
            if (!$user) {
                sendError('Token de autenticação inválido', 401);
            }
            
            // POST /api/vagas
            $data = json_decode(file_get_contents('php://input'), true);
            
            if ($vagaModel->create(
                $data['empresa_id'],
                $data['titulo'],
                $data['descricao'],
                $data['requisitos'],
                $data['beneficios'] ?? '',
                $data['salario_min'] ?? null,
                $data['salario_max'] ?? null,
                $data['tipo_contrato'],
                $data['modalidade'],
                $data['nivel_experiencia'],
                $data['area'],
                $data['localizacao'],
                $data['data_limite'] ?? null
            )) {
                sendResponse(['message' => 'Vaga criada com sucesso'], 201);
            } else {
                sendError('Erro ao criar vaga', 500);
            }
            break;
            
        default:
            sendError('Método não permitido', 405);
    }
}

function handleCandidatosAPI($method, $segments) {
    $candidatoModel = new Candidato();
    
    switch ($method) {
        case 'GET':
            if (isset($segments[1])) {
                // GET /api/candidatos/{id}
                $candidato = $candidatoModel->findById($segments[1]);
                if ($candidato) {
                    // Remover dados sensíveis
                    unset($candidato['telefone'], $candidato['data_nascimento']);
                    sendResponse($candidato);
                } else {
                    sendError('Candidato não encontrado', 404);
                }
            } else {
                // GET /api/candidatos
                $filters = [
                    'search' => $_GET['search'] ?? '',
                    'cidade' => $_GET['cidade'] ?? '',
                    'estado' => $_GET['estado'] ?? '',
                    'disponibilidade' => $_GET['disponibilidade'] ?? ''
                ];
                
                $limit = min(50, intval($_GET['limit'] ?? 20));
                $offset = intval($_GET['offset'] ?? 0);
                
                $candidatos = $candidatoModel->getAll($limit, $offset, $filters);
                
                // Remover dados sensíveis
                foreach ($candidatos as &$candidato) {
                    unset($candidato['telefone'], $candidato['data_nascimento']);
                }
                
                sendResponse(['candidatos' => $candidatos, 'total' => count($candidatos)]);
            }
            break;
            
        default:
            sendError('Método não permitido', 405);
    }
}

function handleEmpresasAPI($method, $segments) {
    $empresaModel = new Empresa();
    
    switch ($method) {
        case 'GET':
            if (isset($segments[1])) {
                // GET /api/empresas/{id}
                $empresa = $empresaModel->findById($segments[1]);
                if ($empresa) {
                    sendResponse($empresa);
                } else {
                    sendError('Empresa não encontrada', 404);
                }
            } else {
                // GET /api/empresas
                $filters = [
                    'search' => $_GET['search'] ?? '',
                    'setor' => $_GET['setor'] ?? '',
                    'porte' => $_GET['porte'] ?? '',
                    'cidade' => $_GET['cidade'] ?? ''
                ];
                
                $limit = min(50, intval($_GET['limit'] ?? 20));
                $offset = intval($_GET['offset'] ?? 0);
                
                $empresas = $empresaModel->getAll($limit, $offset, $filters);
                sendResponse(['empresas' => $empresas, 'total' => count($empresas)]);
            }
            break;
            
        default:
            sendError('Método não permitido', 405);
    }
}

function handleCandidaturasAPI($method, $segments) {
    $candidaturaModel = new Candidatura();
    
    switch ($method) {
        case 'GET':
            if (isset($segments[1])) {
                // GET /api/candidaturas/{id}
                $candidatura = $candidaturaModel->findById($segments[1]);
                if ($candidatura) {
                    sendResponse($candidatura);
                } else {
                    sendError('Candidatura não encontrada', 404);
                }
            } else {
                // GET /api/candidaturas
                $vaga_id = $_GET['vaga_id'] ?? null;
                $candidato_id = $_GET['candidato_id'] ?? null;
                
                if ($vaga_id) {
                    $candidaturas = $candidaturaModel->findByVagaId($vaga_id);
                } elseif ($candidato_id) {
                    $candidaturas = $candidaturaModel->findByCandidatoId($candidato_id);
                } else {
                    sendError('Parâmetro vaga_id ou candidato_id é obrigatório', 400);
                }
                
                sendResponse(['candidaturas' => $candidaturas, 'total' => count($candidaturas)]);
            }
            break;
            
        default:
            sendError('Método não permitido', 405);
    }
}

function handleStatsAPI($method, $segments) {
    if ($method !== 'GET') {
        sendError('Método não permitido', 405);
    }
    
    $userModel = new User();
    $vagaModel = new Vaga();
    $candidaturaModel = new Candidatura();
    $empresaModel = new Empresa();
    $candidatoModel = new Candidato();
    
    $stats = [
        'usuarios' => $userModel->getStats(),
        'vagas' => $vagaModel->getStats(),
        'candidaturas' => $candidaturaModel->getStats(),
        'empresas' => $empresaModel->getStats(),
        'candidatos' => $candidatoModel->getStats()
    ];
    
    sendResponse($stats);
}

function handleSearchAPI($method, $segments) {
    if ($method !== 'GET') {
        sendError('Método não permitido', 405);
    }
    
    $query = $_GET['q'] ?? '';
    $type = $_GET['type'] ?? 'vagas';
    
    if (empty($query)) {
        sendError('Parâmetro q é obrigatório', 400);
    }
    
    $results = [];
    
    switch ($type) {
        case 'vagas':
            $vagaModel = new Vaga();
            $filters = ['search' => $query];
            $results = $vagaModel->getAll(20, 0, $filters);
            break;
            
        case 'candidatos':
            $candidatoModel = new Candidato();
            $filters = ['search' => $query];
            $results = $candidatoModel->getAll(20, 0, $filters);
            break;
            
        case 'empresas':
            $empresaModel = new Empresa();
            $filters = ['search' => $query];
            $results = $empresaModel->getAll(20, 0, $filters);
            break;
            
        default:
            sendError('Tipo de busca inválido', 400);
    }
    
    sendResponse(['results' => $results, 'total' => count($results)]);
}
?>

